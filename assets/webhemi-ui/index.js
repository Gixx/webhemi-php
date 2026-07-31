"use client";

// node_modules/clsx/dist/clsx.mjs
function r(e) {
  var t, f, n = "";
  if ("string" == typeof e || "number" == typeof e) n += e;
  else if ("object" == typeof e) if (Array.isArray(e)) {
    var o = e.length;
    for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
  } else for (f in e) e[f] && (n && (n += " "), n += f);
  return n;
}
function clsx() {
  for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
  return n;
}

// src/lib/cn.ts
function cn(...inputs) {
  return clsx(inputs);
}

// src/shared/components/Button/Button.tsx
import { jsx, jsxs } from "react/jsx-runtime";
var variantClass = {
  primary: "bg-[var(--wh-color-accent)] text-white hover:brightness-110 border-transparent",
  secondary: "bg-[var(--wh-color-surface)] text-[var(--wh-color-ink)] border-[var(--wh-color-line)] hover:border-[var(--wh-color-accent)]",
  ghost: "bg-transparent text-[var(--wh-color-ink-soft)] border-transparent hover:bg-[var(--wh-color-surface)]",
  danger: "bg-[var(--wh-color-danger)] text-white border-transparent hover:brightness-110"
};
var sizeClass = {
  sm: "px-3 py-1.5 text-sm",
  md: "px-4 py-2 text-base",
  lg: "px-5 py-2.5 text-lg"
};
function Button({
  variant = "primary",
  size = "md",
  loading = false,
  disabled,
  className,
  children,
  type = "button",
  ...rest
}) {
  return /* @__PURE__ */ jsxs(
    "button",
    {
      type,
      className: cn(
        "wh-ui inline-flex items-center justify-center gap-2 rounded-[var(--wh-radius-sm)] border font-medium transition disabled:cursor-not-allowed disabled:opacity-50",
        variantClass[variant],
        sizeClass[size],
        className
      ),
      disabled: disabled || loading,
      "aria-busy": loading || void 0,
      ...rest,
      children: [
        loading ? /* @__PURE__ */ jsx("span", { "aria-hidden": "true", children: "\u2026" }) : null,
        children
      ]
    }
  );
}

// src/shared/components/Input/Input.tsx
import { jsx as jsx2 } from "react/jsx-runtime";
function Input({ className, invalid, id, ...rest }) {
  return /* @__PURE__ */ jsx2(
    "input",
    {
      id,
      className: cn(
        "wh-ui w-full rounded-[var(--wh-radius-sm)] border bg-[var(--wh-color-surface)] px-3 py-2 text-base outline-none transition focus:border-[var(--wh-color-accent)] focus:ring-2 focus:ring-[var(--wh-color-accent)]/20",
        invalid ? "border-[var(--wh-color-danger)]" : "border-[var(--wh-color-line)]",
        className
      ),
      "aria-invalid": invalid || void 0,
      ...rest
    }
  );
}

// src/shared/components/Label/Label.tsx
import { jsx as jsx3, jsxs as jsxs2 } from "react/jsx-runtime";
function Label({ children, required, className, ...rest }) {
  return /* @__PURE__ */ jsxs2(
    "label",
    {
      className: cn(
        "wh-ui mb-1 block text-sm font-semibold text-[var(--wh-color-ink-soft)]",
        className
      ),
      ...rest,
      children: [
        children,
        required ? /* @__PURE__ */ jsx3("span", { className: "ml-1 text-[var(--wh-color-accent-hot)]", "aria-hidden": "true", children: "*" }) : null
      ]
    }
  );
}

// src/shared/components/Select/Select.tsx
import { jsx as jsx4 } from "react/jsx-runtime";
function Select({ className, invalid, children, ...rest }) {
  return /* @__PURE__ */ jsx4(
    "select",
    {
      className: cn(
        "wh-ui w-full rounded-[var(--wh-radius-sm)] border bg-[var(--wh-color-surface)] px-3 py-2 text-base outline-none focus:border-[var(--wh-color-accent)] focus:ring-2 focus:ring-[var(--wh-color-accent)]/20",
        invalid ? "border-[var(--wh-color-danger)]" : "border-[var(--wh-color-line)]",
        className
      ),
      "aria-invalid": invalid || void 0,
      ...rest,
      children
    }
  );
}

// src/shared/components/Badge/Badge.tsx
import { jsx as jsx5 } from "react/jsx-runtime";
var toneClass = {
  neutral: "bg-[var(--wh-color-line)] text-[var(--wh-color-ink)]",
  success: "bg-[color-mix(in_srgb,var(--wh-color-success)_18%,white)] text-[var(--wh-color-success)]",
  warning: "bg-[color-mix(in_srgb,var(--wh-color-warning)_18%,white)] text-[var(--wh-color-warning)]",
  danger: "bg-[color-mix(in_srgb,var(--wh-color-danger)_18%,white)] text-[var(--wh-color-danger)]",
  accent: "bg-[color-mix(in_srgb,var(--wh-color-accent)_18%,white)] text-[var(--wh-color-accent)]"
};
function Badge({ tone = "neutral", className, children, ...rest }) {
  return /* @__PURE__ */ jsx5(
    "span",
    {
      className: cn(
        "wh-ui inline-flex items-center rounded-[var(--wh-radius-sm)] px-2 py-0.5 text-xs font-semibold uppercase tracking-wide",
        toneClass[tone],
        className
      ),
      ...rest,
      children
    }
  );
}

// src/shared/components/Icon/Icon.tsx
import { jsx as jsx6, jsxs as jsxs3 } from "react/jsx-runtime";
var paths = {
  dashboard: "M3 3h8v8H3V3zm10 0h8v5h-8V3zM3 13h5v8H3v-8zm7 0h11v8H10v-8z",
  users: "M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5z",
  sites: "M4 4h16v4H4V4zm0 6h10v10H4V10zm12 0h4v10h-4V10z",
  hosts: "M12 2L2 7l10 5 10-5-10-5zm0 9L2 7v10l10 5 10-5V7l-10 4z",
  roles: "M12 1l3 6h7l-5.5 4.5L18 19l-6-3.5L6 19l1.5-7.5L2 7h7l3-6z",
  settings: "M12 8a4 4 0 1 0 4 4 4 4 0 0 0-4-4zm9 4a7.9 7.9 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a8.2 8.2 0 0 0-2.1-1.2L16 2h-4l-.4 2.6a8.2 8.2 0 0 0-2.1 1.2l-2.4-1-2 3.4 2 1.6A7.9 7.9 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1a8.2 8.2 0 0 0 2.1 1.2L12 22h4l.4-2.6a8.2 8.2 0 0 0 2.1-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2z",
  logout: "M10 4H4v16h6v-2H6V6h4V4zm3.5 4l-1.4 1.4L15.7 12l-3.6 2.6L13.5 16 20 12l-6.5-4z",
  check: "M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z",
  alert: "M12 2 1 21h22L12 2zm0 6h.01v6H12V8zm0 8h.01v2H12v-2z",
  chevron: "M9 6l6 6-6 6"
};
function Icon({ name, title, className, ...rest }) {
  return /* @__PURE__ */ jsxs3(
    "svg",
    {
      viewBox: "0 0 24 24",
      width: "1em",
      height: "1em",
      className: cn("wh-ui inline-block shrink-0 fill-current", className),
      role: title ? "img" : "presentation",
      "aria-hidden": title ? void 0 : true,
      ...rest,
      children: [
        title ? /* @__PURE__ */ jsx6("title", { children: title }) : null,
        /* @__PURE__ */ jsx6("path", { d: paths[name] })
      ]
    }
  );
}

// src/shared/components/FormField/FormField.tsx
import { jsx as jsx7, jsxs as jsxs4 } from "react/jsx-runtime";
function FormField({
  label,
  htmlFor,
  required,
  error,
  hint,
  children,
  className
}) {
  return /* @__PURE__ */ jsxs4("div", { className: cn("wh-ui mb-4", className), children: [
    /* @__PURE__ */ jsx7(Label, { htmlFor, required, children: label }),
    children,
    hint && !error ? /* @__PURE__ */ jsx7("p", { className: "mt-1 text-sm text-[var(--wh-color-muted)]", children: hint }) : null,
    error ? /* @__PURE__ */ jsx7("p", { className: "mt-1 text-sm text-[var(--wh-color-danger)]", role: "alert", children: error }) : null
  ] });
}

// src/shared/components/Alert/Alert.tsx
import { jsx as jsx8, jsxs as jsxs5 } from "react/jsx-runtime";
var toneClass2 = {
  info: "border-[var(--wh-color-accent)] bg-[color-mix(in_srgb,var(--wh-color-accent)_10%,white)]",
  success: "border-[var(--wh-color-success)] bg-[color-mix(in_srgb,var(--wh-color-success)_10%,white)]",
  warning: "border-[var(--wh-color-warning)] bg-[color-mix(in_srgb,var(--wh-color-warning)_10%,white)]",
  danger: "border-[var(--wh-color-danger)] bg-[color-mix(in_srgb,var(--wh-color-danger)_10%,white)]"
};
function Alert({ tone = "info", title, children, className, ...rest }) {
  return /* @__PURE__ */ jsxs5(
    "div",
    {
      role: "status",
      className: cn(
        "wh-ui flex gap-3 rounded-[var(--wh-radius-md)] border-l-4 px-4 py-3",
        toneClass2[tone],
        className
      ),
      ...rest,
      children: [
        /* @__PURE__ */ jsx8(Icon, { name: tone === "success" ? "check" : "alert", className: "mt-0.5 text-lg" }),
        /* @__PURE__ */ jsxs5("div", { children: [
          title ? /* @__PURE__ */ jsx8("p", { className: "font-semibold", children: title }) : null,
          /* @__PURE__ */ jsx8("div", { className: "text-sm text-[var(--wh-color-ink-soft)]", children })
        ] })
      ]
    }
  );
}

// src/admin/chrome/_lib/underlineAccessKey.tsx
import { Fragment, jsx as jsx9, jsxs as jsxs6 } from "react/jsx-runtime";
function underlineAccessKey(text, key) {
  if (!key) {
    return text;
  }
  const index = text.toLowerCase().indexOf(key.toLowerCase());
  if (index === -1) {
    return text;
  }
  const end = index + key.length;
  return /* @__PURE__ */ jsxs6(Fragment, { children: [
    text.slice(0, index),
    /* @__PURE__ */ jsx9("u", { children: text.slice(index, end) }),
    text.slice(end)
  ] });
}

// src/admin/chrome/Button/Button.tsx
import { jsx as jsx10 } from "react/jsx-runtime";
function Button2({
  isDefault = false,
  loading = false,
  className,
  children,
  type = "button",
  disabled,
  accessKey,
  ...rest
}) {
  const content = loading || !accessKey || typeof children !== "string" ? children : underlineAccessKey(children, accessKey);
  return /* @__PURE__ */ jsx10(
    "button",
    {
      type,
      className: cn(isDefault && "default", className),
      disabled: disabled || loading,
      "aria-busy": loading || void 0,
      accessKey: accessKey || void 0,
      ...rest,
      children: loading ? "\u2026" : content
    }
  );
}
function VerticalBar({ className, ...rest }) {
  return /* @__PURE__ */ jsx10("div", { className: cn("vertical-bar", className), ...rest });
}

// src/admin/chrome/_lib/fieldBox.tsx
import { Fragment as Fragment2, jsx as jsx11, jsxs as jsxs7 } from "react/jsx-runtime";
function renderFieldBox({
  id,
  label,
  control,
  controlFirst = false,
  labelPosition = "before",
  boxClassName
}) {
  const labelEl = /* @__PURE__ */ jsx11("label", { htmlFor: id, children: label });
  const above = !controlFirst && labelPosition === "above";
  return /* @__PURE__ */ jsx11(
    "div",
    {
      className: cn(
        "field-box",
        controlFirst && "field-box-control-first",
        above && "field-box-above",
        boxClassName
      ),
      children: controlFirst ? /* @__PURE__ */ jsxs7(Fragment2, { children: [
        control,
        labelEl
      ] }) : /* @__PURE__ */ jsxs7(Fragment2, { children: [
        labelEl,
        control
      ] })
    }
  );
}

// src/admin/chrome/TextBox/TextBox.tsx
import { jsx as jsx12 } from "react/jsx-runtime";
function TextBox({
  className,
  type = "text",
  label,
  labelPosition = "before",
  boxClassName,
  accessKey,
  id,
  ...rest
}) {
  const control = /* @__PURE__ */ jsx12(
    "input",
    {
      id,
      type,
      className: cn(className),
      accessKey: accessKey || void 0,
      ...rest
    }
  );
  if (label == null) {
    return control;
  }
  if (!id) {
    throw new Error("TextBox requires an id when label is set");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  return renderFieldBox({
    id,
    label: caption,
    control,
    labelPosition,
    boxClassName
  });
}
TextBox.displayName = "TextBox";

// src/admin/chrome/TextArea/TextArea.tsx
import { jsx as jsx13 } from "react/jsx-runtime";
function TextArea({
  className,
  resizable = "none",
  style,
  label,
  labelPosition = "before",
  boxClassName,
  accessKey,
  id,
  ...rest
}) {
  const control = /* @__PURE__ */ jsx13(
    "textarea",
    {
      id,
      className: cn(className),
      style: { ...style, resize: resizable },
      accessKey: accessKey || void 0,
      ...rest
    }
  );
  if (label == null) {
    return control;
  }
  if (!id) {
    throw new Error("TextArea requires an id when label is set");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  return renderFieldBox({
    id,
    label: caption,
    control,
    labelPosition,
    boxClassName
  });
}
TextArea.displayName = "TextArea";

// src/admin/chrome/Checkbox/Checkbox.tsx
import { jsx as jsx14 } from "react/jsx-runtime";
function Checkbox({
  id,
  label,
  className,
  boxClassName,
  accessKey,
  ...rest
}) {
  if (!id) {
    throw new Error("Checkbox requires an id so the label can use htmlFor");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  const control = /* @__PURE__ */ jsx14(
    "input",
    {
      id,
      type: "checkbox",
      className: cn(className),
      accessKey: accessKey || void 0,
      ...rest
    }
  );
  return renderFieldBox({
    id,
    label: caption,
    control,
    controlFirst: true,
    boxClassName
  });
}
Checkbox.displayName = "Checkbox";

// src/admin/chrome/Radio/Radio.tsx
import { jsx as jsx15 } from "react/jsx-runtime";
function Radio({
  id,
  label,
  className,
  boxClassName,
  accessKey,
  ...rest
}) {
  if (!id) {
    throw new Error("Radio requires an id so the label can use htmlFor");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  const control = /* @__PURE__ */ jsx15(
    "input",
    {
      id,
      type: "radio",
      className: cn(className),
      accessKey: accessKey || void 0,
      ...rest
    }
  );
  return renderFieldBox({
    id,
    label: caption,
    control,
    controlFirst: true,
    boxClassName
  });
}
Radio.displayName = "Radio";

// src/admin/chrome/Select/Select.tsx
import { jsx as jsx16 } from "react/jsx-runtime";
function Select2({
  className,
  children,
  label,
  labelPosition = "before",
  boxClassName,
  accessKey,
  id,
  ...rest
}) {
  const control = /* @__PURE__ */ jsx16(
    "select",
    {
      id,
      className: cn(className),
      accessKey: accessKey || void 0,
      ...rest,
      children
    }
  );
  if (label == null) {
    return control;
  }
  if (!id) {
    throw new Error("Select requires an id when label is set");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  return renderFieldBox({
    id,
    label: caption,
    control,
    labelPosition,
    boxClassName
  });
}
Select2.displayName = "Select";

// src/admin/chrome/Slider/Slider.tsx
import { jsx as jsx17 } from "react/jsx-runtime";
function Slider({
  boxIndicator,
  className,
  label,
  labelPosition = "before",
  boxClassName,
  accessKey,
  id,
  ...rest
}) {
  const control = /* @__PURE__ */ jsx17(
    "input",
    {
      id,
      type: "range",
      className: cn(boxIndicator && "has-box-indicator", className),
      accessKey: accessKey || void 0,
      ...rest
    }
  );
  if (label == null) {
    return control;
  }
  if (!id) {
    throw new Error("Slider requires an id when label is set");
  }
  const caption = accessKey && (typeof label === "string" || typeof label === "number") ? underlineAccessKey(String(label), accessKey) : label;
  return renderFieldBox({
    id,
    label: caption,
    control,
    labelPosition,
    boxClassName
  });
}
Slider.displayName = "Slider";

// src/admin/chrome/FieldRow/FieldRow.tsx
import { jsx as jsx18 } from "react/jsx-runtime";
function FieldRow({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx18("div", { className: cn("field-row", className), ...rest, children });
}

// src/admin/chrome/GroupBox/GroupBox.tsx
import { jsx as jsx19, jsxs as jsxs8 } from "react/jsx-runtime";
function GroupBox({ legend, className, children, ...rest }) {
  return /* @__PURE__ */ jsxs8("fieldset", { className: cn(className), ...rest, children: [
    legend != null ? /* @__PURE__ */ jsx19("legend", { children: legend }) : null,
    children
  ] });
}

// src/admin/chrome/Window/Window.tsx
import { jsx as jsx20 } from "react/jsx-runtime";
function Window({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("window", className), ...rest, children });
}
function TitleBar({ inactive = false, className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("title-bar", inactive && "inactive", className), ...rest, children });
}
function TitleBarText({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("title-bar-text", className), ...rest, children });
}
function TitleBarControls({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("title-bar-controls", className), ...rest, children });
}
function TitleBarControl({ action, className, type = "button", ...rest }) {
  return /* @__PURE__ */ jsx20("button", { type, "aria-label": action, className: cn(className), ...rest });
}
function WindowBody({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("window-body", className), ...rest, children });
}
function StatusBar({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("div", { className: cn("status-bar", className), ...rest, children });
}
function StatusBarField({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx20("p", { className: cn("status-bar-field", className), ...rest, children });
}

// src/admin/chrome/Tabs/Tabs.tsx
import { jsx as jsx21 } from "react/jsx-runtime";
function TabList({ multirows = false, className, children, ...rest }) {
  return /* @__PURE__ */ jsx21("menu", { role: "tablist", className: cn(multirows && "multirows", className), ...rest, children });
}
function TabRow({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx21("div", { className: cn("tab-row", className), role: "presentation", ...rest, children });
}
function Tab({ selected = false, href = "#", className, children, ...rest }) {
  return /* @__PURE__ */ jsx21("li", { role: "tab", "aria-selected": selected, className: cn(className), ...rest, children: /* @__PURE__ */ jsx21("a", { href, children }) });
}
function TabPanel({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx21("div", { role: "tabpanel", className: cn("window", className), ...rest, children });
}

// src/admin/chrome/_lib/promoteTabRow.ts
function promoteTabRow(rows, rowIndex, columnIndex) {
  if (rows.length === 0) {
    return { rows: [], selectedIndex: 0 };
  }
  const next = rows.map((row2) => [...row2]);
  const clampedRow = Math.max(0, Math.min(rows.length - 1, rowIndex));
  const [row] = next.splice(clampedRow, 1);
  next.push(row);
  const selectedIndex = Math.max(0, Math.min(row.length - 1, columnIndex));
  return { rows: next, selectedIndex };
}

// src/admin/chrome/TreeView/TreeView.tsx
import { jsx as jsx22 } from "react/jsx-runtime";
function TreeView({ className, children, ...rest }) {
  return /* @__PURE__ */ jsx22("ul", { className: cn("tree-view", className), ...rest, children });
}

// src/admin/chrome/Scrollable/Scrollable.tsx
import { useRef } from "react";

// src/admin/chrome/_lib/useCustomScrollbar.ts
import { useEffect } from "react";

// src/admin/chrome/_lib/attachCustomScrollbar.ts
var THUMB_MIN = 17;
var ARROW_STEP = 24;
var REPEAT_DELAY_MS = 400;
var REPEAT_EVERY_MS = 50;
function el(tag, className) {
  const node = document.createElement(tag);
  if (className) {
    node.className = className;
  }
  return node;
}
function buildAxis(axis) {
  const root = el("div", `sb sb-${axis}`);
  root.setAttribute("aria-hidden", "true");
  const dec = el("button", "sb-btn sb-dec");
  dec.type = "button";
  dec.tabIndex = -1;
  const track = el("div", "sb-track");
  const thumb = el("div", "sb-thumb");
  track.appendChild(thumb);
  const inc = el("button", "sb-btn sb-inc");
  inc.type = "button";
  inc.tabIndex = -1;
  root.append(dec, track, inc);
  return { root, dec, track, thumb, inc };
}
function attachCustomScrollbar(host, viewport) {
  if (host.classList.contains("has-custom-scrollbar")) {
    return () => void 0;
  }
  host.classList.add("has-custom-scrollbar");
  const y = buildAxis("y");
  const x = buildAxis("x");
  const corner = el("div", "sb sb-corner");
  corner.setAttribute("aria-hidden", "true");
  y.root.hidden = true;
  x.root.hidden = true;
  corner.hidden = true;
  host.append(y.root, x.root, corner);
  let drag = null;
  let repeatTimer = 0;
  let repeatDelay = 0;
  let raf = 0;
  const needsY = () => viewport.scrollHeight > viewport.clientHeight + 1;
  const needsX = () => viewport.scrollWidth > viewport.clientWidth + 1;
  const update = () => {
    const canScrollY = needsY();
    const canScrollX = needsX();
    host.classList.toggle("sb-show-y", canScrollY);
    host.classList.toggle("sb-show-x", canScrollX);
    y.root.hidden = !canScrollY;
    x.root.hidden = !canScrollX;
    corner.hidden = !(canScrollY && canScrollX);
    if (canScrollY) {
      const view = viewport.clientHeight;
      const size = viewport.scrollHeight;
      const trackSize = y.track.clientHeight;
      const thumbSize = Math.max(THUMB_MIN, Math.round(view / Math.max(size, 1) * trackSize));
      const maxScroll = Math.max(0, size - view);
      const maxThumb = Math.max(0, trackSize - thumbSize);
      const top = maxScroll === 0 ? 0 : viewport.scrollTop / maxScroll * maxThumb;
      y.thumb.hidden = false;
      y.thumb.style.height = `${thumbSize}px`;
      y.thumb.style.transform = `translateY(${top}px)`;
      y.dec.disabled = viewport.scrollTop <= 0;
      y.inc.disabled = viewport.scrollTop >= maxScroll - 1;
    }
    if (canScrollX) {
      const view = viewport.clientWidth;
      const size = viewport.scrollWidth;
      const trackSize = x.track.clientWidth;
      const thumbSize = Math.max(THUMB_MIN, Math.round(view / Math.max(size, 1) * trackSize));
      const maxScroll = Math.max(0, size - view);
      const maxThumb = Math.max(0, trackSize - thumbSize);
      const left = maxScroll === 0 ? 0 : viewport.scrollLeft / maxScroll * maxThumb;
      x.thumb.hidden = false;
      x.thumb.style.width = `${thumbSize}px`;
      x.thumb.style.transform = `translateX(${left}px)`;
      x.dec.disabled = viewport.scrollLeft <= 0;
      x.inc.disabled = viewport.scrollLeft >= maxScroll - 1;
    }
  };
  const scheduleUpdate = () => {
    if (raf) {
      return;
    }
    raf = requestAnimationFrame(() => {
      raf = 0;
      update();
    });
  };
  const scrollByAxis = (axis, delta) => {
    if (axis === "y") {
      viewport.scrollTop += delta;
    } else {
      viewport.scrollLeft += delta;
    }
    update();
  };
  const stopRepeat = () => {
    window.clearTimeout(repeatDelay);
    window.clearInterval(repeatTimer);
    repeatDelay = 0;
    repeatTimer = 0;
  };
  const startRepeat = (axis, delta) => {
    stopRepeat();
    scrollByAxis(axis, delta);
    repeatDelay = window.setTimeout(() => {
      repeatTimer = window.setInterval(() => scrollByAxis(axis, delta), REPEAT_EVERY_MS);
    }, REPEAT_DELAY_MS);
  };
  const onArrowDown = (button, axis, delta, event) => {
    if (event.button !== 0 || button.disabled) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    startRepeat(axis, delta);
  };
  const onYDec = (e) => onArrowDown(y.dec, "y", -ARROW_STEP, e);
  const onYInc = (e) => onArrowDown(y.inc, "y", ARROW_STEP, e);
  const onXDec = (e) => onArrowDown(x.dec, "x", -ARROW_STEP, e);
  const onXInc = (e) => onArrowDown(x.inc, "x", ARROW_STEP, e);
  y.dec.addEventListener("pointerdown", onYDec);
  y.inc.addEventListener("pointerdown", onYInc);
  x.dec.addEventListener("pointerdown", onXDec);
  x.inc.addEventListener("pointerdown", onXInc);
  const onTrackPointerDown = (axis, thumb, event) => {
    if (event.button !== 0 || event.target === thumb || thumb.hidden) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    const thumbRect = thumb.getBoundingClientRect();
    if (axis === "y") {
      const page = viewport.clientHeight;
      if (event.clientY < thumbRect.top) {
        startRepeat("y", -page);
      } else if (event.clientY > thumbRect.bottom) {
        startRepeat("y", page);
      }
    } else {
      const page = viewport.clientWidth;
      if (event.clientX < thumbRect.left) {
        startRepeat("x", -page);
      } else if (event.clientX > thumbRect.right) {
        startRepeat("x", page);
      }
    }
  };
  const onYTrack = (e) => onTrackPointerDown("y", y.thumb, e);
  const onXTrack = (e) => onTrackPointerDown("x", x.thumb, e);
  y.track.addEventListener("pointerdown", onYTrack);
  x.track.addEventListener("pointerdown", onXTrack);
  const onThumbDown = (axis, event) => {
    if (event.button !== 0) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    stopRepeat();
    drag = {
      axis,
      startPos: axis === "y" ? event.clientY : event.clientX,
      startScroll: axis === "y" ? viewport.scrollTop : viewport.scrollLeft
    };
    event.currentTarget.setPointerCapture?.(event.pointerId);
  };
  const onYThumb = (e) => onThumbDown("y", e);
  const onXThumb = (e) => onThumbDown("x", e);
  y.thumb.addEventListener("pointerdown", onYThumb);
  x.thumb.addEventListener("pointerdown", onXThumb);
  const onPointerMove = (event) => {
    if (!drag) {
      return;
    }
    const { axis, startPos, startScroll } = drag;
    if (axis === "y") {
      const trackSize = y.track.clientHeight;
      const thumbSize = y.thumb.offsetHeight;
      const maxThumb = Math.max(1, trackSize - thumbSize);
      const maxScroll = Math.max(1, viewport.scrollHeight - viewport.clientHeight);
      const delta = event.clientY - startPos;
      viewport.scrollTop = startScroll + delta / maxThumb * maxScroll;
    } else {
      const trackSize = x.track.clientWidth;
      const thumbSize = x.thumb.offsetWidth;
      const maxThumb = Math.max(1, trackSize - thumbSize);
      const maxScroll = Math.max(1, viewport.scrollWidth - viewport.clientWidth);
      const delta = event.clientX - startPos;
      viewport.scrollLeft = startScroll + delta / maxThumb * maxScroll;
    }
    update();
  };
  const onPointerUp = () => {
    drag = null;
    stopRepeat();
  };
  const stopDeskBubbling = (event) => {
    event.stopPropagation();
  };
  for (const node of [y.root, x.root, corner]) {
    node.addEventListener("pointerdown", stopDeskBubbling);
    node.addEventListener("mousedown", stopDeskBubbling);
  }
  viewport.addEventListener("scroll", scheduleUpdate, { passive: true });
  window.addEventListener("pointermove", onPointerMove);
  window.addEventListener("pointerup", onPointerUp);
  window.addEventListener("pointercancel", onPointerUp);
  window.addEventListener("blur", onPointerUp);
  const resizeObserver = new ResizeObserver(scheduleUpdate);
  resizeObserver.observe(host);
  resizeObserver.observe(viewport);
  const mutationObserver = new MutationObserver((records) => {
    for (const record of records) {
      if (record.type === "childList" && Array.from(record.addedNodes).concat(Array.from(record.removedNodes)).some(
        (node) => node.nodeType === 1 && (node.classList?.contains("sb") || node.classList?.contains("scrollable-viewport"))
      )) {
        continue;
      }
      scheduleUpdate();
      break;
    }
  });
  mutationObserver.observe(viewport, { childList: true, subtree: true, characterData: true });
  requestAnimationFrame(() => {
    update();
    requestAnimationFrame(update);
  });
  return () => {
    stopRepeat();
    if (raf) {
      cancelAnimationFrame(raf);
    }
    resizeObserver.disconnect();
    mutationObserver.disconnect();
    window.removeEventListener("pointermove", onPointerMove);
    window.removeEventListener("pointerup", onPointerUp);
    window.removeEventListener("pointercancel", onPointerUp);
    window.removeEventListener("blur", onPointerUp);
    viewport.removeEventListener("scroll", scheduleUpdate);
    y.root.remove();
    x.root.remove();
    corner.remove();
    host.classList.remove("has-custom-scrollbar", "sb-show-y", "sb-show-x");
  };
}

// src/admin/chrome/_lib/useCustomScrollbar.ts
function useCustomScrollbar(hostRef, viewportRef) {
  useEffect(() => {
    const host = hostRef.current;
    const viewport = viewportRef.current;
    if (!host || !viewport) {
      return;
    }
    return attachCustomScrollbar(host, viewport);
  }, [hostRef, viewportRef]);
}

// src/admin/chrome/Scrollable/Scrollable.tsx
import { jsx as jsx23 } from "react/jsx-runtime";
function Scrollable({
  className,
  children,
  viewportClassName,
  ...rest
}) {
  const hostRef = useRef(null);
  const viewportRef = useRef(null);
  useCustomScrollbar(hostRef, viewportRef);
  return /* @__PURE__ */ jsx23("div", { ref: hostRef, className: cn("scrollable", className), ...rest, children: /* @__PURE__ */ jsx23("div", { ref: viewportRef, className: cn("scrollable-viewport", viewportClassName), children }) });
}

// src/admin/chrome/SunkenPanel/SunkenPanel.tsx
import { jsx as jsx24 } from "react/jsx-runtime";
function SunkenPanel({
  scrollable = false,
  className,
  children,
  ...rest
}) {
  if (scrollable) {
    return /* @__PURE__ */ jsx24(Scrollable, { className: cn("sunken-panel", className), ...rest, children });
  }
  return /* @__PURE__ */ jsx24("div", { className: cn("sunken-panel", className), ...rest, children: /* @__PURE__ */ jsx24("div", { className: "scrollable-viewport", children }) });
}

// src/admin/chrome/FieldBorder/FieldBorder.tsx
import { jsx as jsx25 } from "react/jsx-runtime";
function FieldBorder({
  disabled = false,
  scrollable = false,
  className,
  children,
  ...rest
}) {
  const borderClass = disabled ? "field-border-disabled" : "field-border";
  if (scrollable) {
    return /* @__PURE__ */ jsx25(Scrollable, { className: cn(borderClass, className), ...rest, children });
  }
  return /* @__PURE__ */ jsx25("div", { className: cn(borderClass, className), ...rest, children });
}

// src/admin/chrome/Table/Table.tsx
import { useRef as useRef2 } from "react";

// src/admin/chrome/_lib/useTableView.ts
import { useEffect as useEffect2 } from "react";
var HIGHLIGHTED = "highlighted";
function isBodyRow(element) {
  return element instanceof HTMLTableRowElement && element.parentElement?.tagName === "TBODY";
}
function useTableView(tableRef, enabled = true) {
  useEffect2(() => {
    const table = tableRef.current;
    if (!enabled || !table) {
      return;
    }
    if (table.dataset.interactiveBound === "true") {
      return;
    }
    table.dataset.interactiveBound = "true";
    const onClick = (event) => {
      const newlySelectedRow = event.composedPath().find(isBodyRow);
      if (!newlySelectedRow) {
        return;
      }
      const tbody = newlySelectedRow.parentElement;
      if (!tbody) {
        return;
      }
      const previouslySelectedRow = Array.from(tbody.children).filter(isBodyRow).find((row) => row.classList.contains(HIGHLIGHTED));
      if (previouslySelectedRow && previouslySelectedRow !== newlySelectedRow) {
        previouslySelectedRow.classList.remove(HIGHLIGHTED);
      }
      newlySelectedRow.classList.toggle(HIGHLIGHTED);
    };
    table.addEventListener("click", onClick);
    return () => {
      table.removeEventListener("click", onClick);
      delete table.dataset.interactiveBound;
    };
  }, [tableRef, enabled]);
}

// src/admin/chrome/Table/Table.tsx
import { jsx as jsx26 } from "react/jsx-runtime";
function Table({ interactive = false, className, children, ...rest }) {
  const ref = useRef2(null);
  useTableView(ref, interactive);
  return /* @__PURE__ */ jsx26("table", { ref, className: cn(interactive && "interactive", className), ...rest, children });
}
function TableRow({ highlighted = false, className, children, ...rest }) {
  return /* @__PURE__ */ jsx26("tr", { className: cn(highlighted && "highlighted", className), ...rest, children });
}

// src/admin/chrome/Progress/Progress.tsx
import { jsx as jsx27 } from "react/jsx-runtime";
function Progress({ value = 0, segmented = false, className, ...rest }) {
  const clamped = Math.max(0, Math.min(100, value));
  return /* @__PURE__ */ jsx27("div", { className: cn("progress-indicator", segmented && "segmented", className), ...rest, children: /* @__PURE__ */ jsx27("span", { className: "progress-indicator-bar", style: { width: `${clamped}%` } }) });
}

// src/admin/chrome/SystemIcon/SystemIcon.tsx
import { jsx as jsx28 } from "react/jsx-runtime";
function SystemIcon({
  kind,
  label,
  labelTone = "light",
  href = "#",
  description,
  draggable = false,
  onActivate,
  onOpen,
  className,
  linkProps,
  onDoubleClick,
  ...rest
}) {
  const handleClick = (event) => {
    linkProps?.onClick?.(event);
    if (event.defaultPrevented) {
      return;
    }
    if (href === "#") {
      event.preventDefault();
    }
    onActivate?.();
  };
  const handleDoubleClick = (event) => {
    onDoubleClick?.(event);
    if (event.defaultPrevented) {
      return;
    }
    onActivate?.();
    onOpen?.();
  };
  return /* @__PURE__ */ jsx28(
    "div",
    {
      className: cn("icon", kind, `label-tone-${labelTone}`, className),
      draggable,
      onDoubleClick: handleDoubleClick,
      ...rest,
      children: /* @__PURE__ */ jsx28(
        "a",
        {
          href,
          "data-description": description,
          ...linkProps,
          onClick: handleClick,
          children: /* @__PURE__ */ jsx28("span", { children: label })
        }
      )
    }
  );
}

// src/admin/bricks/_lib/PaneWindowShell.tsx
import { jsx as jsx29, jsxs as jsxs9 } from "react/jsx-runtime";
var TITLE_BAR_ICON_OPTIONS = [
  "none",
  "control-panel",
  "site",
  "network-neighborhood",
  "users",
  "roles",
  "permissions",
  "hosts",
  "sites",
  "settings",
  "themes"
];
function resolveTitleBarIcon(value) {
  return value && value !== "none" ? value : void 0;
}
function PaneWindowShell({
  title,
  titleIcon,
  titleBarControls,
  inactive = false,
  statusBar,
  children,
  resizable = false,
  className,
  bodyClassName,
  width,
  style,
  ...rest
}) {
  const mergedStyle = width !== void 0 ? { ...style, width } : style;
  const controls = titleBarControls === null ? null : titleBarControls ?? /* @__PURE__ */ jsx29(TitleBarControls, { children: /* @__PURE__ */ jsx29(TitleBarControl, { action: "Close" }) });
  return /* @__PURE__ */ jsxs9(Window, { className: cn(resizable && "resizable", className), style: mergedStyle, ...rest, children: [
    /* @__PURE__ */ jsxs9(TitleBar, { inactive, children: [
      /* @__PURE__ */ jsx29(TitleBarText, { className: titleIcon, children: title }),
      controls
    ] }),
    /* @__PURE__ */ jsx29(WindowBody, { className: bodyClassName, children }),
    statusBar
  ] });
}

// src/admin/bricks/DialogWindow/DialogWindow.tsx
import { Fragment as Fragment3, jsx as jsx30, jsxs as jsxs10 } from "react/jsx-runtime";
function DialogWindow({
  banner,
  type = "none",
  children,
  actions,
  className,
  ...shell
}) {
  const typed = type !== "none";
  return /* @__PURE__ */ jsx30(PaneWindowShell, { className: cn("w-window-sm", className), ...shell, children: /* @__PURE__ */ jsxs10("div", { className: "window-pane dialog-panel-layout", children: [
    banner ? /* @__PURE__ */ jsx30("div", { className: "panel banner", children: banner }) : null,
    /* @__PURE__ */ jsx30("div", { className: cn("panel", typed && "dialog-typed"), children: typed ? /* @__PURE__ */ jsxs10(Fragment3, { children: [
      /* @__PURE__ */ jsx30("span", { className: cn("dialog-icon", `dialog-icon--${type}`), "aria-hidden": true }),
      /* @__PURE__ */ jsx30("div", { className: "dialog-body", children })
    ] }) : children }),
    actions ? /* @__PURE__ */ jsx30("div", { className: "panel actions", children: actions }) : null
  ] }) });
}

// src/admin/bricks/IconPanelWindow/IconPanelWindow.tsx
import { jsx as jsx31, jsxs as jsxs11 } from "react/jsx-runtime";
function IconPanelWindow({
  info,
  infoUnselected = false,
  children,
  className,
  paneHeight = 280,
  resizable = true,
  ...shell
}) {
  const paneStyle = {
    height: typeof paneHeight === "number" ? `${paneHeight}px` : paneHeight
  };
  return /* @__PURE__ */ jsx31(
    PaneWindowShell,
    {
      className: cn("w-window-xl", className),
      resizable,
      ...shell,
      children: /* @__PURE__ */ jsxs11(FieldBorder, { scrollable: true, className: "window-pane icon-panel-layout", style: paneStyle, children: [
        /* @__PURE__ */ jsx31("div", { className: cn("panel info", infoUnselected && "unselected"), children: info }),
        /* @__PURE__ */ jsx31("div", { className: "panel icon-list", children })
      ] })
    }
  );
}

// src/admin/bricks/IconPanelWindow/IconPanelSelectionInfo.tsx
import { Fragment as Fragment4, jsx as jsx32, jsxs as jsxs12 } from "react/jsx-runtime";
function IconPanelSelectionInfo({
  kind,
  label,
  description
}) {
  return /* @__PURE__ */ jsxs12(Fragment4, { children: [
    /* @__PURE__ */ jsx32("span", { className: cn("info-icon", kind), "aria-hidden": true }),
    /* @__PURE__ */ jsx32("h1", { className: "info-title", children: label }),
    /* @__PURE__ */ jsx32("hr", { className: "info-separator" }),
    /* @__PURE__ */ jsx32("p", { className: "info-description", children: description })
  ] });
}

// src/admin/bricks/WizardWindow/WizardWindow.tsx
import { jsx as jsx33, jsxs as jsxs13 } from "react/jsx-runtime";
function WizardWindow({
  banner,
  info,
  actions,
  className,
  ...shell
}) {
  return /* @__PURE__ */ jsx33(PaneWindowShell, { className: cn("w-window-md", className), ...shell, children: /* @__PURE__ */ jsxs13("div", { className: "window-pane wizard-panel-layout", children: [
    /* @__PURE__ */ jsx33("div", { className: "panel banner", children: banner }),
    /* @__PURE__ */ jsx33("div", { className: "panel info", children: info }),
    /* @__PURE__ */ jsx33("div", { className: "panel actions", children: actions })
  ] }) });
}

// src/admin/lib/assetPaths.ts
var ADMIN_ASSETS_BASE = "/assets/admin";
function adminAsset(path) {
  const normalized = path.replace(/^\/+/, "");
  return `${ADMIN_ASSETS_BASE}/${normalized}`;
}
function adminIconAsset(kind) {
  return adminAsset(`icons/${kind.replace(/-/g, "_")}.svg`);
}

// src/admin/components/FlashList/FlashList.tsx
import { jsx as jsx34 } from "react/jsx-runtime";
function toneForFlash(key) {
  if (key === "success") {
    return "success";
  }
  if (key === "warning") {
    return "warning";
  }
  if (key === "info") {
    return "info";
  }
  return "danger";
}
function FlashList({ flashes }) {
  if (!flashes) {
    return null;
  }
  return Object.entries(flashes).flatMap(
    ([tone, messages]) => messages.map((message, index) => /* @__PURE__ */ jsx34(Alert, { tone: toneForFlash(tone), className: "mb-4", children: message }, `${tone}-${index}`))
  );
}

// src/admin/components/DataTable/DataTable.tsx
import { jsx as jsx35, jsxs as jsxs14 } from "react/jsx-runtime";
function DataTable({
  columns,
  rows,
  rowKey,
  emptyMessage = "No records found.",
  loading = false,
  className
}) {
  if (loading) {
    return /* @__PURE__ */ jsx35("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: "Loading\u2026" });
  }
  if (rows.length === 0) {
    return /* @__PURE__ */ jsx35("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-dashed border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: emptyMessage });
  }
  return /* @__PURE__ */ jsx35(
    "div",
    {
      className: cn(
        "wh-ui overflow-x-auto rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs14("table", { className: "w-full border-collapse text-left text-sm", children: [
        /* @__PURE__ */ jsx35("thead", { className: "bg-[var(--wh-color-canvas)] text-[var(--wh-color-muted)]", children: /* @__PURE__ */ jsx35("tr", { children: columns.map((col) => /* @__PURE__ */ jsx35("th", { className: cn("px-4 py-3 font-semibold", col.className), children: col.header }, col.key)) }) }),
        /* @__PURE__ */ jsx35("tbody", { children: rows.map((row) => /* @__PURE__ */ jsx35(
          "tr",
          {
            className: "border-t border-[var(--wh-color-line)] hover:bg-[color-mix(in_srgb,var(--wh-color-accent)_6%,white)]",
            children: columns.map((col) => /* @__PURE__ */ jsx35("td", { className: cn("px-4 py-3", col.className), children: col.render(row) }, col.key))
          },
          rowKey(row)
        )) })
      ] })
    }
  );
}

// src/admin/components/Pagination/Pagination.tsx
import { jsx as jsx36, jsxs as jsxs15 } from "react/jsx-runtime";
function Pagination({ page, pageCount, onPageChange, className }) {
  if (pageCount <= 1) {
    return null;
  }
  return /* @__PURE__ */ jsxs15(
    "nav",
    {
      className: cn("wh-ui mt-4 flex items-center justify-between gap-3", className),
      "aria-label": "Pagination",
      children: [
        /* @__PURE__ */ jsx36(
          Button,
          {
            variant: "secondary",
            size: "sm",
            disabled: page <= 1,
            onClick: () => onPageChange(page - 1),
            children: "Previous"
          }
        ),
        /* @__PURE__ */ jsxs15("span", { className: "text-sm text-[var(--wh-color-muted)]", children: [
          "Page ",
          page,
          " of ",
          pageCount
        ] }),
        /* @__PURE__ */ jsx36(
          Button,
          {
            variant: "secondary",
            size: "sm",
            disabled: page >= pageCount,
            onClick: () => onPageChange(page + 1),
            children: "Next"
          }
        )
      ]
    }
  );
}

// src/admin/components/Modal/Modal.tsx
import { useEffect as useEffect3 } from "react";
import { jsx as jsx37, jsxs as jsxs16 } from "react/jsx-runtime";
function Modal({ open, title, children, onClose, footer, className }) {
  useEffect3(() => {
    if (!open) {
      return;
    }
    const onKey = (event) => {
      if (event.key === "Escape") {
        onClose();
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open, onClose]);
  if (!open) {
    return null;
  }
  return /* @__PURE__ */ jsxs16("div", { className: "wh-ui fixed inset-0 z-50 flex items-center justify-center p-4", children: [
    /* @__PURE__ */ jsx37(
      "button",
      {
        type: "button",
        className: "absolute inset-0 bg-[var(--wh-color-ink)]/50",
        "aria-label": "Close dialog",
        onClick: onClose
      }
    ),
    /* @__PURE__ */ jsxs16(
      "div",
      {
        role: "dialog",
        "aria-modal": "true",
        "aria-labelledby": "wh-modal-title",
        className: cn(
          "relative z-10 w-full max-w-lg rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] shadow-lg",
          className
        ),
        children: [
          /* @__PURE__ */ jsxs16("div", { className: "flex items-center justify-between border-b border-[var(--wh-color-line)] px-4 py-3", children: [
            /* @__PURE__ */ jsx37("h2", { id: "wh-modal-title", className: "font-[family-name:var(--wh-font-display)] text-lg", children: title }),
            /* @__PURE__ */ jsx37(Button, { variant: "ghost", size: "sm", onClick: onClose, "aria-label": "Close", children: "\xD7" })
          ] }),
          /* @__PURE__ */ jsx37("div", { className: "px-4 py-4", children }),
          footer ? /* @__PURE__ */ jsx37("div", { className: "flex justify-end gap-2 border-t border-[var(--wh-color-line)] px-4 py-3", children: footer }) : null
        ]
      }
    )
  ] });
}

// src/admin/components/PageHeader/PageHeader.tsx
import { jsx as jsx38, jsxs as jsxs17 } from "react/jsx-runtime";
function PageHeader({ title, description, actions, className }) {
  return /* @__PURE__ */ jsxs17(
    "header",
    {
      className: cn(
        "wh-ui mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-[var(--wh-color-line)] pb-4",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs17("div", { children: [
          /* @__PURE__ */ jsx38("h1", { className: "font-[family-name:var(--wh-font-display)] text-3xl tracking-tight text-[var(--wh-color-ink)]", children: title }),
          description ? /* @__PURE__ */ jsx38("p", { className: "mt-1 max-w-2xl text-[var(--wh-color-muted)]", children: description }) : null
        ] }),
        actions ? /* @__PURE__ */ jsx38("div", { className: "flex flex-wrap gap-2", children: actions }) : null
      ]
    }
  );
}

// src/admin/components/Sidebar/Sidebar.tsx
import { jsx as jsx39, jsxs as jsxs18 } from "react/jsx-runtime";
function Sidebar({ brand = "WebHemi", items, className }) {
  return /* @__PURE__ */ jsxs18(
    "aside",
    {
      className: cn(
        "wh-ui flex min-h-full w-60 flex-col border-r border-[var(--wh-color-line)] bg-[var(--wh-color-ink)] text-white",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs18("div", { className: "border-b border-white/10 px-5 py-5", children: [
          /* @__PURE__ */ jsx39("p", { className: "font-[family-name:var(--wh-font-display)] text-2xl tracking-tight", children: brand }),
          /* @__PURE__ */ jsx39("p", { className: "text-xs uppercase tracking-[0.2em] text-white/60", children: "Admin" })
        ] }),
        /* @__PURE__ */ jsx39("nav", { className: "flex flex-1 flex-col gap-1 p-3", "aria-label": "Admin", children: items.map((item) => /* @__PURE__ */ jsxs18(
          "a",
          {
            href: item.href,
            className: cn(
              "flex items-center gap-3 rounded-[var(--wh-radius-sm)] px-3 py-2 text-sm transition",
              item.active ? "bg-[var(--wh-color-accent)] text-white" : "text-white/80 hover:bg-white/10 hover:text-white"
            ),
            "aria-current": item.active ? "page" : void 0,
            children: [
              item.icon ? /* @__PURE__ */ jsx39(Icon, { name: item.icon }) : null,
              item.label
            ]
          },
          item.id
        )) })
      ]
    }
  );
}

// src/admin/components/TopBar/TopBar.tsx
import { jsx as jsx40, jsxs as jsxs19 } from "react/jsx-runtime";
function TopBar({ title, userLabel, actions, className }) {
  return /* @__PURE__ */ jsxs19(
    "div",
    {
      className: cn(
        "wh-ui flex items-center justify-between gap-4 border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] px-6 py-3",
        className
      ),
      children: [
        /* @__PURE__ */ jsx40("p", { className: "text-sm font-medium text-[var(--wh-color-muted)]", children: title ?? "Control panel" }),
        /* @__PURE__ */ jsxs19("div", { className: "flex items-center gap-3", children: [
          actions,
          userLabel ? /* @__PURE__ */ jsx40("span", { className: "rounded-[var(--wh-radius-sm)] bg-[var(--wh-color-canvas)] px-3 py-1 text-sm", children: userLabel }) : null
        ] })
      ]
    }
  );
}

// src/admin/components/AdminLayout/AdminLayout.tsx
import { jsx as jsx41, jsxs as jsxs20 } from "react/jsx-runtime";
function AdminLayout({
  brand,
  navItems,
  userLabel,
  topBarTitle,
  topBarActions,
  children,
  className
}) {
  return /* @__PURE__ */ jsxs20("div", { className: cn("wh-ui flex min-h-screen bg-[var(--wh-color-canvas)]", className), children: [
    /* @__PURE__ */ jsx41(Sidebar, { brand, items: navItems }),
    /* @__PURE__ */ jsxs20("div", { className: "flex min-w-0 flex-1 flex-col", children: [
      /* @__PURE__ */ jsx41(TopBar, { title: topBarTitle, userLabel, actions: topBarActions }),
      /* @__PURE__ */ jsx41("main", { className: "flex-1 p-6", children })
    ] })
  ] });
}

// src/admin/components/LoginForm/LoginForm.tsx
import { jsx as jsx42, jsxs as jsxs21 } from "react/jsx-runtime";
function LoginForm({
  action = "/login",
  method = "post",
  csrfToken,
  csrfFieldName = "_csrf_token",
  error,
  loading = false,
  emailDefault = "",
  bannerUrl,
  onSubmit,
  className
}) {
  const handleSubmit = (event) => {
    if (!onSubmit) {
      return;
    }
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    onSubmit({
      email: String(data.get("email") ?? ""),
      password: String(data.get("password") ?? ""),
      remember: data.get("remember") === "on"
    });
  };
  const bannerSrc = bannerUrl || adminAsset("system/banner-dialog-login.gif");
  return /* @__PURE__ */ jsxs21("form", { action, method, onSubmit: handleSubmit, className: cn(className), children: [
    csrfToken ? /* @__PURE__ */ jsx42("input", { type: "hidden", name: csrfFieldName, value: csrfToken }) : null,
    /* @__PURE__ */ jsxs21(
      DialogWindow,
      {
        title: "Sign in \u2014 WebHemi CMS Admin",
        titleBarControls: null,
        banner: /* @__PURE__ */ jsx42("img", { alt: "", className: "dialog-banner", src: bannerSrc }),
        actions: /* @__PURE__ */ jsx42(FieldRow, { className: "justify-end", children: /* @__PURE__ */ jsx42(Button2, { type: "submit", isDefault: true, loading, children: "OK" }) }),
        children: [
          error ? /* @__PURE__ */ jsx42("p", { role: "alert", style: { marginTop: 0, marginBottom: 10, color: "#800000" }, children: error }) : null,
          /* @__PURE__ */ jsx42(FieldRow, { children: /* @__PURE__ */ jsx42(
            TextBox,
            {
              id: "email",
              name: "email",
              type: "email",
              label: "Email:",
              accessKey: "e",
              autoComplete: "username",
              defaultValue: emailDefault,
              required: true,
              className: "w-window-xs"
            }
          ) }),
          /* @__PURE__ */ jsx42(FieldRow, { children: /* @__PURE__ */ jsx42(
            TextBox,
            {
              id: "password",
              name: "password",
              type: "password",
              label: "Password:",
              accessKey: "p",
              autoComplete: "current-password",
              required: true,
              className: "w-window-xs"
            }
          ) })
        ]
      }
    )
  ] });
}

// src/admin/components/ControlPanel/ControlPanel.tsx
import { useState } from "react";
import { jsx as jsx43, jsxs as jsxs22 } from "react/jsx-runtime";
var ICONS = [
  { kind: "sites", label: "Sites", description: "Manage sites and their contents." },
  { kind: "hosts", label: "Hosts", description: "Add, remove and verify domains." },
  { kind: "roles", label: "Roles", description: "Add/remove custom roles." },
  { kind: "permissions", label: "Permissions", description: "Manage permissions." },
  { kind: "users", label: "Users", description: "Manage administrative users." },
  { kind: "settings", label: "Settings", description: "General settings for the admin area." },
  { kind: "themes", label: "Themes", description: "Manage frontend themes." }
];
function ControlPanel({
  onClose,
  onMinimize,
  onMaximize,
  onActivate,
  className,
  style,
  width = 600,
  paneHeight = 300
}) {
  const [selected, setSelected] = useState(null);
  return /* @__PURE__ */ jsx43(
    IconPanelWindow,
    {
      className,
      style,
      width,
      draggable: true,
      resizable: true,
      paneHeight,
      title: "Control Panel",
      titleIcon: "control-panel",
      titleBarControls: /* @__PURE__ */ jsxs22(TitleBarControls, { children: [
        /* @__PURE__ */ jsx43(TitleBarControl, { action: "Minimize", onClick: onMinimize }),
        /* @__PURE__ */ jsx43(TitleBarControl, { action: "Maximize", onClick: onMaximize }),
        /* @__PURE__ */ jsx43(TitleBarControl, { action: "Close", onClick: onClose })
      ] }),
      onMouseDown: onActivate,
      infoUnselected: !selected,
      info: selected ? /* @__PURE__ */ jsx43(
        IconPanelSelectionInfo,
        {
          kind: selected.kind,
          label: selected.label,
          description: selected.description
        }
      ) : null,
      statusBar: /* @__PURE__ */ jsxs22(StatusBar, { children: [
        /* @__PURE__ */ jsxs22(StatusBarField, { children: [
          ICONS.length,
          " items"
        ] }),
        /* @__PURE__ */ jsx43(StatusBarField, { className: "description", children: selected?.description ?? "" }),
        /* @__PURE__ */ jsx43(StatusBarField, {})
      ] }),
      children: ICONS.map((icon) => /* @__PURE__ */ jsx43(
        SystemIcon,
        {
          kind: icon.kind,
          label: icon.label,
          labelTone: "dark",
          description: icon.description,
          onActivate: () => setSelected(icon)
        },
        icon.kind
      ))
    }
  );
}

// src/admin/views/SiteListView.tsx
import { jsx as jsx44, jsxs as jsxs23 } from "react/jsx-runtime";
function SiteListView({
  sites,
  loading = false,
  createHref = "/admin/sites/new",
  editHref = (site) => `/admin/sites/${site.id}`
}) {
  return /* @__PURE__ */ jsxs23("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx44(
      PageHeader,
      {
        title: "Sites",
        description: "Multi-tenant sites bound to one or more hostnames.",
        actions: /* @__PURE__ */ jsx44("a", { href: createHref, children: /* @__PURE__ */ jsx44(Button, { children: "New site" }) })
      }
    ),
    /* @__PURE__ */ jsx44(
      DataTable,
      {
        loading,
        rows: sites,
        rowKey: (row) => row.id,
        emptyMessage: "No sites yet. Create the first tenant.",
        columns: [
          { key: "name", header: "Name", render: (row) => row.name },
          { key: "slug", header: "Slug", render: (row) => /* @__PURE__ */ jsx44("code", { children: row.slug }) },
          {
            key: "hosts",
            header: "Hosts",
            render: (row) => String(row.hostCount)
          },
          {
            key: "status",
            header: "Status",
            render: (row) => /* @__PURE__ */ jsx44(Badge, { tone: row.enabled ? "success" : "neutral", children: row.enabled ? "Enabled" : "Disabled" })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx44("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/SiteHostListView.tsx
import { jsx as jsx45, jsxs as jsxs24 } from "react/jsx-runtime";
var statusTone = {
  pending: "warning",
  verified: "accent",
  active: "success"
};
function SiteHostListView({
  hosts,
  loading = false,
  createHref = "/admin/hosts/new",
  verifyHref = (host) => `/admin/hosts/${host.id}/verify`
}) {
  return /* @__PURE__ */ jsxs24("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx45(
      PageHeader,
      {
        title: "Hosts",
        description: "Domain names mapped to sites and surfaces (admin, site, api).",
        actions: /* @__PURE__ */ jsx45("a", { href: createHref, children: /* @__PURE__ */ jsx45(Button, { children: "Add host" }) })
      }
    ),
    /* @__PURE__ */ jsx45(
      DataTable,
      {
        loading,
        rows: hosts,
        rowKey: (row) => row.id,
        emptyMessage: "No hosts configured.",
        columns: [
          { key: "host", header: "Hostname", render: (row) => /* @__PURE__ */ jsx45("code", { children: row.host }) },
          { key: "site", header: "Site", render: (row) => row.siteName },
          {
            key: "surface",
            header: "Surface",
            render: (row) => /* @__PURE__ */ jsx45(Badge, { tone: "accent", children: row.surface })
          },
          {
            key: "status",
            header: "Status",
            render: (row) => /* @__PURE__ */ jsx45(Badge, { tone: statusTone[row.status], children: row.status })
          },
          {
            key: "actions",
            header: "",
            render: (row) => row.status === "pending" ? /* @__PURE__ */ jsx45("a", { href: verifyHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Verify" }) : "\u2014"
          }
        ]
      }
    )
  ] });
}

// src/admin/views/UserListView.tsx
import { jsx as jsx46, jsxs as jsxs25 } from "react/jsx-runtime";
function UserListView({
  users,
  loading = false,
  createHref = "/admin/users/new",
  editHref = (user) => `/admin/users/${user.id}`
}) {
  return /* @__PURE__ */ jsxs25("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx46(
      PageHeader,
      {
        title: "Users",
        description: "Accounts with global roles and optional per-site assignments.",
        actions: /* @__PURE__ */ jsx46("a", { href: createHref, children: /* @__PURE__ */ jsx46(Button, { children: "New user" }) })
      }
    ),
    /* @__PURE__ */ jsx46(
      DataTable,
      {
        loading,
        rows: users,
        rowKey: (row) => row.id,
        emptyMessage: "No users yet.",
        columns: [
          { key: "email", header: "Email", render: (row) => row.email },
          {
            key: "roles",
            header: "Roles",
            render: (row) => /* @__PURE__ */ jsx46("div", { className: "flex flex-wrap gap-1", children: row.roles.map((role) => /* @__PURE__ */ jsx46(Badge, { children: role }, role)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx46("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/RoleListView.tsx
import { jsx as jsx47, jsxs as jsxs26 } from "react/jsx-runtime";
function RoleListView({
  roles,
  loading = false,
  createHref = "/admin/roles/new",
  editHref = (role) => `/admin/roles/${role.id}`
}) {
  return /* @__PURE__ */ jsxs26("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx47(
      PageHeader,
      {
        title: "Roles & permissions",
        description: "RBAC roles with permission strings such as site.list.",
        actions: /* @__PURE__ */ jsx47("a", { href: createHref, children: /* @__PURE__ */ jsx47(Button, { children: "New role" }) })
      }
    ),
    /* @__PURE__ */ jsx47(
      DataTable,
      {
        loading,
        rows: roles,
        rowKey: (row) => row.id,
        emptyMessage: "No roles defined.",
        columns: [
          { key: "name", header: "Role", render: (row) => row.name },
          {
            key: "permissions",
            header: "Permissions",
            render: (row) => /* @__PURE__ */ jsx47("div", { className: "flex flex-wrap gap-1", children: row.permissions.map((permission) => /* @__PURE__ */ jsx47(Badge, { tone: "accent", children: permission }, permission)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx47("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/pages/LoginPage.tsx
import { jsx as jsx48 } from "react/jsx-runtime";
function LoginPage({
  action,
  csrfToken,
  csrfFieldName,
  emailDefault,
  error,
  bannerUrl
}) {
  return /* @__PURE__ */ jsx48(
    LoginForm,
    {
      action,
      csrfToken,
      csrfFieldName,
      emailDefault: emailDefault || "",
      error: error || void 0,
      bannerUrl
    }
  );
}

// src/admin/pages/AdminDesktop.tsx
import { useRef as useRef3, useState as useState2 } from "react";
import { jsx as jsx49, jsxs as jsxs27 } from "react/jsx-runtime";
var CASCADE_ORIGIN = { left: 120, top: 32 };
var CASCADE_STEP = 28;
function AdminDesktop({ sites = [], className }) {
  const nextZRef = useRef3(10);
  const cascadeRef = useRef3(0);
  const [controlPanel, setControlPanel] = useState2(null);
  const [openSites, setOpenSites] = useState2([]);
  const allocatePlacement = () => {
    const index = cascadeRef.current;
    cascadeRef.current += 1;
    nextZRef.current += 1;
    return {
      left: CASCADE_ORIGIN.left + index * CASCADE_STEP,
      top: CASCADE_ORIGIN.top + index * CASCADE_STEP,
      z: nextZRef.current
    };
  };
  const raiseZ = () => {
    nextZRef.current += 1;
    return nextZRef.current;
  };
  const openControlPanel = () => {
    setControlPanel((prev) => {
      if (prev) {
        return { ...prev, z: raiseZ() };
      }
      return allocatePlacement();
    });
  };
  const closeControlPanel = () => setControlPanel(null);
  const openSite = (site) => {
    setOpenSites((prev) => {
      const existing = prev.find((w) => w.id === site.id);
      if (existing) {
        const z = raiseZ();
        return prev.map((w) => w.id === site.id ? { ...w, z } : w);
      }
      const place = allocatePlacement();
      return [...prev, { id: site.id, name: site.name, ...place }];
    });
  };
  const closeSite = (id) => {
    setOpenSites((prev) => prev.filter((w) => w.id !== id));
  };
  const activateSite = (id) => {
    setOpenSites((prev) => {
      const target = prev.find((w) => w.id === id);
      if (!target || target.z === nextZRef.current) {
        return prev;
      }
      const z = raiseZ();
      return prev.map((w) => w.id === id ? { ...w, z } : w);
    });
  };
  const activateControlPanel = () => {
    setControlPanel((prev) => {
      if (!prev || prev.z === nextZRef.current) {
        return prev;
      }
      return { ...prev, z: raiseZ() };
    });
  };
  return /* @__PURE__ */ jsxs27("div", { className: cn("dashboard", className), children: [
    /* @__PURE__ */ jsxs27("div", { className: "icon-list", children: [
      sites.map((site) => /* @__PURE__ */ jsx49(
        SystemIcon,
        {
          kind: "site",
          label: site.name,
          labelTone: "light",
          description: site.slug ? `Site: ${site.slug}` : void 0,
          onOpen: () => openSite(site)
        },
        site.id
      )),
      /* @__PURE__ */ jsx49(
        SystemIcon,
        {
          kind: "control-panel",
          label: "Control Panel",
          labelTone: "light",
          description: "Configure WebHemi administration.",
          onOpen: openControlPanel
        }
      )
    ] }),
    controlPanel ? /* @__PURE__ */ jsx49(
      "div",
      {
        className: "desktop-window",
        style: { left: controlPanel.left, top: controlPanel.top, zIndex: controlPanel.z },
        children: /* @__PURE__ */ jsx49(ControlPanel, { onClose: closeControlPanel, onActivate: activateControlPanel })
      }
    ) : null,
    openSites.map((win) => /* @__PURE__ */ jsx49(
      "div",
      {
        className: "desktop-window",
        style: { left: win.left, top: win.top, zIndex: win.z },
        onMouseDown: () => activateSite(win.id),
        children: /* @__PURE__ */ jsx49(
          DialogWindow,
          {
            title: win.name,
            titleIcon: "site",
            type: "info",
            titleBarControls: /* @__PURE__ */ jsx49(TitleBarControls, { children: /* @__PURE__ */ jsx49(TitleBarControl, { action: "Close", onClick: () => closeSite(win.id) }) }),
            actions: /* @__PURE__ */ jsx49(FieldRow, { className: "justify-end", children: /* @__PURE__ */ jsx49(Button2, { type: "button", isDefault: true, accessKey: "o", onClick: () => closeSite(win.id), children: "OK" }) }),
            children: /* @__PURE__ */ jsxs27("p", { style: { margin: 0 }, children: [
              "Site administration for ",
              /* @__PURE__ */ jsx49("strong", { children: win.name }),
              " will appear here in a later release."
            ] })
          }
        )
      },
      win.id
    ))
  ] });
}

// src/admin/pages/AdminDashboard.tsx
import { jsx as jsx50, jsxs as jsxs28 } from "react/jsx-runtime";
function AdminDashboard({
  userLabel,
  navItems,
  siteCount = 0,
  hostCount = 0,
  flashes
}) {
  return /* @__PURE__ */ jsxs28(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Dashboard", children: [
    /* @__PURE__ */ jsx50(FlashList, { flashes }),
    /* @__PURE__ */ jsx50(
      PageHeader,
      {
        title: "Dashboard",
        description: "Multi-tenant control panel powered by @webhemi/ui."
      }
    ),
    /* @__PURE__ */ jsxs28("div", { style: { display: "flex", gap: "1.5rem", flexWrap: "wrap" }, children: [
      /* @__PURE__ */ jsxs28(Alert, { tone: "info", title: "Sites", children: [
        siteCount,
        " configured"
      ] }),
      /* @__PURE__ */ jsxs28(Alert, { tone: "info", title: "Hosts", children: [
        hostCount,
        " configured"
      ] })
    ] })
  ] });
}

// src/admin/pages/SitesPage.tsx
import { jsx as jsx51, jsxs as jsxs29 } from "react/jsx-runtime";
function SitesPage({
  userLabel,
  navItems,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs29(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Sites", children: [
    /* @__PURE__ */ jsx51(FlashList, { flashes }),
    /* @__PURE__ */ jsx51(SiteListView, { sites: sites || [], createHref: "#create-site" }),
    canEdit ? /* @__PURE__ */ jsxs29("form", { id: "create-site", action: createAction, method: "post", style: { marginTop: "2rem" }, children: [
      /* @__PURE__ */ jsx51(FormField, { label: "Name", htmlFor: "name", required: true, children: /* @__PURE__ */ jsx51(Input, { id: "name", name: "name", required: true }) }),
      /* @__PURE__ */ jsx51(FormField, { label: "Slug", htmlFor: "slug", required: true, hint: "Lowercase identifier", children: /* @__PURE__ */ jsx51(Input, { id: "slug", name: "slug", required: true }) }),
      /* @__PURE__ */ jsx51(Button, { type: "submit", children: "Create site" })
    ] }) : null
  ] });
}

// src/admin/pages/HostsPage.tsx
import { jsx as jsx52, jsxs as jsxs30 } from "react/jsx-runtime";
function HostsPage({
  userLabel,
  navItems,
  hosts,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs30(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Hosts", children: [
    /* @__PURE__ */ jsx52(FlashList, { flashes }),
    /* @__PURE__ */ jsx52(SiteHostListView, { hosts: hosts || [], createHref: "#create-host" }),
    canEdit ? /* @__PURE__ */ jsxs30("form", { id: "create-host", action: createAction, method: "post", style: { marginTop: "2rem" }, children: [
      /* @__PURE__ */ jsx52(FormField, { label: "Hostname", htmlFor: "host", required: true, children: /* @__PURE__ */ jsx52(Input, { id: "host", name: "host", placeholder: "www.example.com", required: true }) }),
      /* @__PURE__ */ jsx52(FormField, { label: "Site", htmlFor: "site_id", required: true, children: /* @__PURE__ */ jsx52(Select, { id: "site_id", name: "site_id", required: true, children: (sites || []).map((site) => /* @__PURE__ */ jsx52("option", { value: site.id, children: site.name }, site.id)) }) }),
      /* @__PURE__ */ jsx52(FormField, { label: "Surface", htmlFor: "surface", children: /* @__PURE__ */ jsxs30(Select, { id: "surface", name: "surface", defaultValue: "site", children: [
        /* @__PURE__ */ jsx52("option", { value: "admin", children: "admin" }),
        /* @__PURE__ */ jsx52("option", { value: "site", children: "site" }),
        /* @__PURE__ */ jsx52("option", { value: "api", children: "api" })
      ] }) }),
      /* @__PURE__ */ jsx52(Button, { type: "submit", children: "Add host" })
    ] }) : null
  ] });
}

// src/themes/default/components/SiteHeader/SiteHeader.tsx
import { jsx as jsx53, jsxs as jsxs31 } from "react/jsx-runtime";
function SiteHeader({ siteName, navItems = [], actions, className }) {
  return /* @__PURE__ */ jsx53(
    "header",
    {
      className: cn(
        "wh-ui border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs31("div", { className: "mx-auto flex max-w-5xl items-center justify-between gap-6 px-6 py-4", children: [
        /* @__PURE__ */ jsx53(
          "a",
          {
            href: "/",
            className: "font-[family-name:var(--wh-font-display)] text-2xl text-[var(--wh-color-ink)] no-underline",
            children: siteName
          }
        ),
        /* @__PURE__ */ jsx53("nav", { className: "flex flex-1 items-center gap-4", "aria-label": "Primary", children: navItems.map((item) => /* @__PURE__ */ jsx53(
          "a",
          {
            href: item.href,
            className: cn(
              "text-sm no-underline",
              item.active ? "font-semibold text-[var(--wh-color-accent)]" : "text-[var(--wh-color-muted)] hover:text-[var(--wh-color-ink)]"
            ),
            children: item.label
          },
          item.href
        )) }),
        actions ? /* @__PURE__ */ jsx53("div", { className: "flex items-center gap-2", children: actions }) : null
      ] })
    }
  );
}

// src/themes/default/components/Hero/Hero.tsx
import { jsx as jsx54, jsxs as jsxs32 } from "react/jsx-runtime";
function Hero({ title, subtitle, actions, className }) {
  return /* @__PURE__ */ jsxs32(
    "section",
    {
      className: cn(
        "wh-ui relative overflow-hidden bg-[var(--wh-color-ink)] text-[var(--wh-color-surface)]",
        className
      ),
      children: [
        /* @__PURE__ */ jsx54(
          "div",
          {
            className: "pointer-events-none absolute inset-0 opacity-40",
            style: {
              background: "radial-gradient(ellipse at 20% 20%, var(--wh-color-accent) 0%, transparent 55%), radial-gradient(ellipse at 80% 80%, var(--wh-color-accent-hot) 0%, transparent 50%)"
            },
            "aria-hidden": true
          }
        ),
        /* @__PURE__ */ jsxs32("div", { className: "relative mx-auto flex min-h-[70vh] max-w-5xl flex-col justify-end gap-4 px-6 pb-16 pt-24", children: [
          /* @__PURE__ */ jsx54("h1", { className: "max-w-3xl font-[family-name:var(--wh-font-display)] text-5xl leading-tight md:text-6xl", children: title }),
          subtitle ? /* @__PURE__ */ jsx54("p", { className: "max-w-xl text-lg text-[var(--wh-color-canvas)]/90", children: subtitle }) : null,
          actions ? /* @__PURE__ */ jsx54("div", { className: "mt-2 flex flex-wrap gap-3", children: actions }) : null
        ] })
      ]
    }
  );
}
export {
  ADMIN_ASSETS_BASE,
  AdminDashboard,
  AdminDesktop,
  AdminLayout,
  Alert,
  Badge,
  Button2 as Button,
  Checkbox,
  ControlPanel,
  DataTable,
  DialogWindow,
  FieldBorder,
  FieldRow,
  FlashList,
  FormField,
  GroupBox,
  Hero,
  HostsPage,
  Icon,
  IconPanelSelectionInfo,
  IconPanelWindow,
  Input,
  Label,
  LoginForm,
  LoginPage,
  Modal,
  PageHeader,
  Pagination,
  PaneWindowShell,
  Progress,
  Radio,
  RoleListView,
  Scrollable,
  Select2 as Select,
  Sidebar,
  SiteHeader,
  SiteHostListView,
  SiteListView,
  SitesPage,
  Slider,
  StatusBar,
  StatusBarField,
  SunkenPanel,
  SystemIcon,
  TITLE_BAR_ICON_OPTIONS,
  Tab,
  TabList,
  TabPanel,
  TabRow,
  Table,
  TableRow,
  TextArea,
  TextBox,
  TitleBar,
  TitleBarControl,
  TitleBarControls,
  TitleBarText,
  TopBar,
  TreeView,
  UserListView,
  VerticalBar,
  Window,
  WindowBody,
  WizardWindow,
  adminAsset,
  adminIconAsset,
  attachCustomScrollbar,
  promoteTabRow,
  resolveTitleBarIcon,
  useCustomScrollbar,
  useTableView
};
//# sourceMappingURL=index.js.map