import * as catalog from "../data/catalog";
import client from "./client";

/**
 * Contrat admin pour l'API Laravel.
 * Même enveloppe que le site : { data }, erreurs { message, errors? }.
 * En-tête Authorization: Bearer {token} si POST /admin/login renvoie un token.
 * Les textes éditoriaux voyagent dans `translations: { en, fr }`.
 *
 * POST   /admin/login
 * POST   /admin/logout
 * GET    /admin/me
 * GET    /admin/dashboard
 * GET|POST        /admin/products
 * PUT|DELETE      /admin/products/{id}
 * GET|POST        /admin/categories
 * PUT|DELETE      /admin/categories/{id}
 * GET|POST        /admin/colors
 * PUT|DELETE      /admin/colors/{id}
 * GET|POST        /admin/brides
 * PUT|DELETE      /admin/brides/{id}
 * GET|POST        /admin/testimonials
 * PUT|DELETE      /admin/testimonials/{id}
 * GET|PUT         /admin/pages/{slug}
 * GET|PUT         /admin/home                 { salon, salonImages[], hero, ... }
 * GET|PUT         /admin/settings
 * GET             /admin/orders
 * PATCH           /admin/orders/{id}          { status }
 * GET             /admin/appointments
 * PATCH           /admin/appointments/{id}    { status }
 * GET             /admin/contacts
 * PATCH           /admin/contacts/{id}        { read }
 * GET             /admin/newsletter
 * DELETE          /admin/newsletter/{id}
 */

const useMock = import.meta.env.VITE_USE_MOCK !== "false";
const SESSION = "lb_admin";
const TOKEN = "lb_admin_token";
const DEMO = { email: "admin@jaiphat.com", password: "jaiphat", name: "Studio" };

const wait = (data, ms = 70) =>
  new Promise((resolve) => {
    window.setTimeout(() => resolve(structuredClone(data)), ms);
  });

async function live(request) {
  const { data } = await request;
  return data?.data ?? data;
}

function headers() {
  const token = localStorage.getItem(TOKEN);
  return token ? { Authorization: `Bearer ${token}` } : {};
}

function session() {
  try {
    return JSON.parse(localStorage.getItem(SESSION) || "null");
  } catch {
    return null;
  }
}

function run(fn) {
  try {
    return wait(fn());
  } catch (error) {
    return Promise.reject(error);
  }
}

export const demoLogin = useMock ? { email: DEMO.email, password: DEMO.password } : null;

export function adminLogin(payload) {
  if (useMock) {
    const email = String(payload.email || "").trim().toLowerCase();
    if (email !== DEMO.email || payload.password !== DEMO.password) {
      return Promise.reject(new Error("err.auth"));
    }
    const user = { name: DEMO.name, email: DEMO.email };
    localStorage.setItem(SESSION, JSON.stringify(user));
    return wait(user, 120);
  }
  return live(client.post("/admin/login", payload)).then((user) => {
    if (user?.token) localStorage.setItem(TOKEN, user.token);
    localStorage.setItem(SESSION, JSON.stringify({ name: user.name, email: user.email }));
    return user;
  });
}

export function adminLogout() {
  localStorage.removeItem(SESSION);
  localStorage.removeItem(TOKEN);
  if (useMock) return wait(null, 20);
  return live(client.post("/admin/logout", null, { headers: headers() })).catch(() => null);
}

export function getAdmin() {
  if (useMock) {
    const user = session();
    if (!user) return Promise.reject(new Error("err.auth"));
    return wait(user, 20);
  }
  return live(client.get("/admin/me", { headers: headers() }));
}

/** Envoie une photo. Mock : data URL locale. API : POST /admin/uploads -> renvoie l'URL publique. */
export function uploadImage(file) {
  if (useMock) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = () => resolve(String(reader.result || ""));
      reader.onerror = () => resolve("");
      reader.readAsDataURL(file);
    });
  }
  const form = new FormData();
  form.append("file", file);
  return live(client.post("/admin/uploads", form, { headers: headers() })).then((data) => data.url);
}

export function getDashboard() {
  if (useMock) {
    const data = catalog.snapshot();
    return wait({
      products: data.products.length,
      orders: data.orders.length,
      appointments: data.appointments.filter((item) => item.status === "pending").length,
      messages: data.contacts.filter((item) => !item.read).length,
      subscribers: data.subscribers.length,
      recentOrders: data.orders.slice(0, 5),
    });
  }
  return live(client.get("/admin/dashboard", { headers: headers() }));
}

const collections = {
  products: ["products", catalog.saveProduct, catalog.deleteProduct],
  categories: ["categories", catalog.saveCategory, catalog.deleteCategory],
  colors: ["colors", catalog.saveColor, catalog.deleteColor],
  brides: ["brides", catalog.saveBride, catalog.deleteBride],
  testimonials: ["testimonials", catalog.saveTestimonial, catalog.deleteTestimonial],
};

function list(name) {
  if (useMock) return wait(catalog.snapshot()[name]);
  return live(client.get(`/admin/${name}`, { headers: headers() }));
}

function save(name, input) {
  const [, writer] = collections[name];
  if (useMock) return run(() => writer(input));
  const path = input.id ? `/admin/${name}/${input.id}` : `/admin/${name}`;
  const method = input.id ? "put" : "post";
  return live(client.request({ url: path, method, data: input, headers: headers() }));
}

function remove(name, id) {
  const [, , drop] = collections[name];
  if (useMock) return run(() => drop(id));
  return live(client.delete(`/admin/${name}/${id}`, { headers: headers() }));
}

export const getProducts = () => list("products");
export const saveProduct = (input) => save("products", input);
export const deleteProduct = (id) => remove("products", id);

export const getCategories = () => list("categories");
export const saveCategory = (input) => save("categories", input);
export const deleteCategory = (id) => remove("categories", id);

export const getColors = () => list("colors");
export const saveColor = (input) => save("colors", input);
export const deleteColor = (id) => remove("colors", id);

export const getBrides = () => list("brides");
export const saveBride = (input) => save("brides", input);
export const deleteBride = (id) => remove("brides", id);

export const getTestimonials = () => list("testimonials");
export const saveTestimonial = (input) => save("testimonials", input);
export const deleteTestimonial = (id) => remove("testimonials", id);

export function getPage(slug) {
  if (useMock) {
    const page = catalog.snapshot().pages[slug];
    if (!page) return Promise.reject(new Error("admin.page"));
    return wait(page);
  }
  return live(client.get(`/admin/pages/${slug}`, { headers: headers() }));
}

export function savePage(slug, input) {
  if (useMock) return run(() => catalog.savePage(slug, input));
  return live(client.put(`/admin/pages/${slug}`, input, { headers: headers() }));
}

export function getHomeContent() {
  if (useMock) return wait(catalog.snapshot().home);
  return live(client.get("/admin/home", { headers: headers() }));
}

export function saveHomeContent(input) {
  if (useMock) return run(() => catalog.saveHome(input));
  return live(client.put("/admin/home", input, { headers: headers() }));
}

export function getSettings() {
  if (useMock) return wait(catalog.snapshot().settings);
  return live(client.get("/admin/settings", { headers: headers() }));
}

export function saveSettings(input) {
  if (useMock) return run(() => catalog.saveSettings(input));
  return live(client.put("/admin/settings", input, { headers: headers() }));
}

export function resetStudio() {
  if (useMock) return run(() => catalog.resetCatalog());
  return Promise.reject(new Error("admin.generic"));
}

export function getOrders() {
  if (useMock) return wait(catalog.snapshot().orders);
  return live(client.get("/admin/orders", { headers: headers() }));
}

export function updateOrder(id, status) {
  if (useMock) return run(() => catalog.setOrderStatus(id, status));
  return live(client.patch(`/admin/orders/${id}`, { status }, { headers: headers() }));
}

export function getAppointments() {
  if (useMock) return wait(catalog.snapshot().appointments);
  return live(client.get("/admin/appointments", { headers: headers() }));
}

export function updateAppointment(id, status) {
  if (useMock) return run(() => catalog.setAppointmentStatus(id, status));
  return live(client.patch(`/admin/appointments/${id}`, { status }, { headers: headers() }));
}

export function getContacts() {
  if (useMock) return wait(catalog.snapshot().contacts);
  return live(client.get("/admin/contacts", { headers: headers() }));
}

export function updateContact(id, read) {
  if (useMock) return run(() => catalog.setContactRead(id, read));
  return live(client.patch(`/admin/contacts/${id}`, { read }, { headers: headers() }));
}

export function getSubscribers() {
  if (useMock) return wait(catalog.snapshot().subscribers);
  return live(client.get("/admin/newsletter", { headers: headers() }));
}

export function removeSubscriber(id) {
  if (useMock) return run(() => catalog.deleteSubscriber(id));
  return live(client.delete(`/admin/newsletter/${id}`, { headers: headers() }));
}
