import { ChevronLeft, ChevronRight, Star, Upload, X } from "lucide-react";
import { useRef, useState } from "react";
import { Link } from "react-router-dom";
import { uploadImage } from "../api/admin";
import { gallery } from "../data/catalog";
import { useAdmin } from "./copy";

const fieldClass =
  "mt-1 w-full rounded-md border border-line bg-card px-2.5 py-1.5 text-[13px] font-normal normal-case tracking-normal outline-none transition focus:border-blush";

export function PageHead({ kicker, title, action }) {
  return (
    <div className="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-line pb-3">
      <div className="min-w-0">
        {kicker && <p className="text-[11px] text-muted">{kicker}</p>}
        <h1 className="text-[15px] leading-tight font-medium">{title}</h1>
      </div>
      {action}
    </div>
  );
}

export function Notice({ children }) {
  if (!children) return null;
  return <p className="mb-3 rounded-md border border-line bg-card px-3 py-2 text-[13px]">{children}</p>;
}

export function TextField({ label, value, onChange, type = "text", required = false }) {
  return (
    <label className="block text-[12px] font-medium text-muted normal-case">
      {label}
      <input
        type={type}
        required={required}
        value={value ?? ""}
        onChange={(event) => onChange(event.target.value)}
        className={fieldClass}
      />
    </label>
  );
}

export function AreaField({ label, value, onChange, rows = 3 }) {
  return (
    <label className="block text-[12px] font-medium text-muted normal-case">
      {label}
      <textarea
        rows={rows}
        value={value ?? ""}
        onChange={(event) => onChange(event.target.value)}
        className={fieldClass}
      />
    </label>
  );
}

export function SelectField({ label, value, onChange, options }) {
  return (
    <label className="block text-[12px] font-medium text-muted normal-case">
      {label}
      <select value={value ?? ""} onChange={(event) => onChange(event.target.value)} className={fieldClass}>
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
    </label>
  );
}

export function CheckField({ label, checked, onChange }) {
  return (
    <label className="flex items-center gap-2 text-[13px]">
      <input
        type="checkbox"
        checked={Boolean(checked)}
        onChange={(event) => onChange(event.target.checked)}
        className="h-3.5 w-3.5 accent-[var(--color-blush)]"
      />
      {label}
    </label>
  );
}

export function LocaleBar({ locale, setLocale, hint }) {
  return (
    <div className="mb-3 flex flex-wrap items-center justify-between gap-2">
      <p className="text-[12px] text-muted">{hint}</p>
      <div className="flex rounded-md border border-line bg-card p-0.5">
        {["en", "fr"].map((code) => (
          <button
            key={code}
            type="button"
            onClick={() => setLocale(code)}
            className={`rounded px-2.5 py-1 text-[11px] uppercase ${
              locale === code ? "bg-ink text-cream" : "text-muted"
            }`}
          >
            {code}
          </button>
        ))}
      </div>
    </div>
  );
}

export function SaveButton({ children, busy }) {
  return (
    <button
      type="submit"
      disabled={busy}
      className="rounded-md bg-blush px-3 py-1.5 text-[12px] font-medium text-on-accent transition hover:bg-blush-dark disabled:opacity-50"
    >
      {children}
    </button>
  );
}

export function QuietButton({ children, onClick, to, type = "button" }) {
  const className = "inline-flex rounded-md border border-line bg-card px-2.5 py-1.5 text-[12px] font-medium transition hover:border-blush hover:text-blush";
  if (to) return <Link to={to} className={className}>{children}</Link>;
  return (
    <button type={type} onClick={onClick} className={className}>
      {children}
    </button>
  );
}

function readImageFiles(fileList) {
  const files = [...fileList].filter((file) => file.type.startsWith("image/"));
  // Mock : data URL. API Laravel : chaque photo est envoyée au serveur, on récupère son URL publique.
  return Promise.all(files.map((file) => uploadImage(file).catch(() => ""))).then((urls) => urls.filter(Boolean));
}

export function ImagePick({ label, value, onChange, frame = "portrait" }) {
  const { t } = useAdmin();
  const fileRef = useRef(null);
  const [over, setOver] = useState(false);
  const shape = frame === "square" ? "aspect-square" : "aspect-[3/4]";
  const thumb = frame === "square" ? "h-10 w-10" : "h-10 w-8";

  function openPicker() {
    fileRef.current?.click();
  }

  async function onFiles(event) {
    const urls = await readImageFiles(event.target.files || []);
    event.target.value = "";
    if (urls[0]) onChange(urls[0]);
  }

  async function onDrop(event) {
    event.preventDefault();
    setOver(false);
    const urls = await readImageFiles(event.dataTransfer.files || []);
    if (urls[0]) onChange(urls[0]);
  }

  return (
    <div>
      <div className="mb-1.5 flex items-center justify-between gap-2">
        <p className="text-[12px] font-medium text-muted">{label}</p>
        <QuietButton onClick={openPicker}>
          <span className="inline-flex items-center gap-1.5">
            <Upload className="h-3.5 w-3.5" />
            {t("product.upload")}
          </span>
        </QuietButton>
      </div>
      <input ref={fileRef} type="file" accept="image/*" className="hidden" onChange={onFiles} />
      <button
        type="button"
        onClick={openPicker}
        onDragOver={(event) => {
          event.preventDefault();
          setOver(true);
        }}
        onDragLeave={() => setOver(false)}
        onDrop={onDrop}
        className={`relative flex ${shape} w-full items-center justify-center overflow-hidden rounded-md border border-dashed ${over ? "border-blush bg-sand" : "border-line bg-sand/40"}`}
      >
        {value ? (
          <img src={value} alt="" className="absolute inset-0 h-full w-full object-cover" />
        ) : (
          <span className="px-3 text-[13px] text-muted">{t("product.imageEmpty")}</span>
        )}
      </button>
      {value && (
        <button type="button" onClick={() => onChange("")} className="mt-1 text-[12px] text-muted hover:text-blush">
          {t("common.delete")}
        </button>
      )}
      <div className="no-scrollbar mt-2 flex gap-1.5 overflow-x-auto pb-1">
        {gallery.map((src) => (
          <button
            key={src}
            type="button"
            onClick={() => onChange(src)}
            className={`${thumb} shrink-0 overflow-hidden rounded ${value === src ? "ring-2 ring-blush" : "opacity-70"}`}
          >
            <img src={src} alt="" className="h-full w-full object-cover" />
          </button>
        ))}
      </div>
      <input
        value={value || ""}
        onChange={(event) => onChange(event.target.value)}
        placeholder="/images/…"
        className={fieldClass}
      />
    </div>
  );
}

export function ImagesField({ label, values, onChange }) {
  const { t } = useAdmin();
  const fileRef = useRef(null);
  const [path, setPath] = useState("");
  const [over, setOver] = useState(false);
  const picked = values.filter(Boolean);

  function commit(next) {
    onChange(next.length ? next : [""]);
  }

  function addMany(srcs) {
    const extra = srcs.map((src) => src.trim()).filter((src) => src && !picked.includes(src));
    if (!extra.length) return;
    commit([...picked, ...extra]);
    setPath("");
  }

  function add(src) {
    addMany([src]);
  }

  function openPicker() {
    fileRef.current?.click();
  }

  async function onFiles(event) {
    const urls = await readImageFiles(event.target.files || []);
    event.target.value = "";
    addMany(urls);
  }

  async function onDrop(event) {
    event.preventDefault();
    setOver(false);
    addMany(await readImageFiles(event.dataTransfer.files || []));
  }

  function remove(index) {
    commit(picked.filter((_, i) => i !== index));
  }

  function move(index, dir) {
    const target = index + dir;
    if (target < 0 || target >= picked.length) return;
    const next = [...picked];
    [next[index], next[target]] = [next[target], next[index]];
    commit(next);
  }

  function cover(index) {
    if (index === 0) return;
    const next = [...picked];
    const [item] = next.splice(index, 1);
    commit([item, ...next]);
  }

  return (
    <div
      onDragOver={(event) => {
        event.preventDefault();
        setOver(true);
      }}
      onDragLeave={() => setOver(false)}
      onDrop={onDrop}
    >
      <input ref={fileRef} type="file" accept="image/*" multiple className="hidden" onChange={onFiles} />
      <div className="mb-3 flex flex-wrap items-end justify-between gap-2">
        <div>
          <p className="text-[13px] font-medium">{label}</p>
          <p className="mt-0.5 text-[12px] text-muted">{t("product.imageHint")}</p>
        </div>
        <QuietButton onClick={openPicker}>
          <span className="inline-flex items-center gap-1.5">
            <Upload className="h-3.5 w-3.5" />
            {t("product.upload")}
          </span>
        </QuietButton>
      </div>
      {picked.length === 0 ? (
        <button
          type="button"
          onClick={openPicker}
          className={`mb-3 w-full rounded-md border border-dashed px-3 py-10 text-center text-[13px] text-muted ${over ? "border-blush bg-sand" : "border-line"}`}
        >
          {t("product.imageEmpty")}
        </button>
      ) : (
        <div className="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
          {picked.map((src, index) => (
            <article key={`${src}-${index}`} className="overflow-hidden rounded-md border border-line bg-card">
              <div className="relative aspect-[3/4] bg-sand">
                <img src={src} alt="" className="h-full w-full object-cover" />
                {index === 0 && (
                  <span className="absolute top-1 left-1 rounded bg-ink px-1.5 py-0.5 text-[10px] text-cream">{t("product.cover")}</span>
                )}
              </div>
              <div className="flex items-center justify-between gap-0.5 border-t border-line px-1 py-1">
                <button type="button" className="grid h-6 w-6 place-items-center text-muted disabled:opacity-30" disabled={index === 0} aria-label={t("product.moveLeft")} onClick={() => move(index, -1)}>
                  <ChevronLeft className="h-3.5 w-3.5" />
                </button>
                <button type="button" className="grid h-6 w-6 place-items-center text-muted disabled:opacity-30" disabled={index === 0} aria-label={t("product.makeCover")} onClick={() => cover(index)}>
                  <Star className="h-3.5 w-3.5" />
                </button>
                <button type="button" className="grid h-6 w-6 place-items-center text-muted disabled:opacity-30" disabled={index === picked.length - 1} aria-label={t("product.moveRight")} onClick={() => move(index, 1)}>
                  <ChevronRight className="h-3.5 w-3.5" />
                </button>
                <button type="button" className="grid h-6 w-6 place-items-center text-muted hover:text-blush" aria-label={t("common.delete")} onClick={() => remove(index)}>
                  <X className="h-3.5 w-3.5" />
                </button>
              </div>
            </article>
          ))}
          <button type="button" onClick={openPicker} className={`flex aspect-[3/4] flex-col items-center justify-center gap-1 rounded-md border border-dashed text-[12px] text-muted ${over ? "border-blush bg-sand" : "border-line"}`}>
            <Upload className="h-4 w-4" />
            {t("product.upload")}
          </button>
        </div>
      )}
      <p className="text-[12px] font-medium text-muted">{t("product.library")}</p>
      <div className="no-scrollbar mt-2 grid max-h-48 grid-cols-6 gap-1.5 overflow-y-auto sm:grid-cols-8 lg:grid-cols-12">
        {gallery.map((src) => (
          <button
            key={src}
            type="button"
            onClick={() => add(src)}
            className={`aspect-[3/4] overflow-hidden rounded ${picked.includes(src) ? "ring-2 ring-blush" : "opacity-80 hover:opacity-100"}`}
          >
            <img src={src} alt="" className="h-full w-full object-cover" />
          </button>
        ))}
      </div>
      <div className="mt-3 flex gap-2">
        <input
          value={path}
          onChange={(event) => setPath(event.target.value)}
          onKeyDown={(event) => {
            if (event.key === "Enter") {
              event.preventDefault();
              if (path.trim()) add(path);
              else openPicker();
            }
          }}
          placeholder="/images/…"
          className={fieldClass + " mt-0"}
        />
        <QuietButton
          onClick={() => {
            if (path.trim()) add(path);
            else openPicker();
          }}
        >
          {t("product.addPath")}
        </QuietButton>
      </div>
    </div>
  );
}

export function Panel({ children, onSubmit }) {
  return (
    <form onSubmit={onSubmit} className="mb-4 rounded-lg border border-line bg-card p-4">
      {children}
    </form>
  );
}

export function Table({ columns, rows, empty }) {
  if (!rows.length) return <p className="rounded-lg border border-dashed border-line px-3 py-6 text-center text-[13px] text-muted">{empty}</p>;
  return (
    <div className="overflow-x-auto rounded-lg border border-line bg-card">
      <table className="w-full min-w-[640px] text-left text-[13px]">
        <thead className="bg-sand/60 text-[11px] font-medium text-muted">
          <tr>
            {columns.map((column) => (
              <th key={column.key} className="px-3 py-2 font-medium">
                {column.label}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="border-t border-line">
              {columns.map((column) => (
                <td key={column.key} className="px-3 py-2 align-middle">
                  {column.render ? column.render(row) : row[column.key]}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
