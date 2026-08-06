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
function TreeToggle({
  className,
  expanded = false,
  "aria-label": ariaLabel,
  ...rest
}) {
  return /* @__PURE__ */ jsx22(
    "button",
    {
      type: "button",
      className: cn("tree-toggle", className),
      "aria-expanded": expanded,
      "aria-label": ariaLabel ?? (expanded ? "Collapse" : "Expand"),
      ...rest
    }
  );
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
  tone = "system",
  className,
  children,
  ...rest
}) {
  const panelClassName = cn(
    "sunken-panel",
    tone === "white" && "sunken-panel-white",
    className
  );
  if (scrollable) {
    return /* @__PURE__ */ jsx24(Scrollable, { className: panelClassName, ...rest, children });
  }
  return /* @__PURE__ */ jsx24("div", { className: panelClassName, ...rest, children: /* @__PURE__ */ jsx24("div", { className: "scrollable-viewport", children }) });
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
    onActivate?.(event);
  };
  const handleDoubleClick = (event) => {
    onDoubleClick?.(event);
    if (event.defaultPrevented) {
      return;
    }
    onActivate?.(event);
    onOpen?.();
  };
  return /* @__PURE__ */ jsx28(
    "div",
    {
      className: cn("icon", kind, `label-tone-${labelTone}`, className),
      draggable,
      onDoubleClick: handleDoubleClick,
      ...rest,
      children: /* @__PURE__ */ jsx28("a", { href, ...linkProps, onClick: handleClick, children: /* @__PURE__ */ jsx28("span", { children: label }) })
    }
  );
}

// src/admin/bricks/_lib/PaneWindowShell.tsx
import { jsx as jsx29, jsxs as jsxs9 } from "react/jsx-runtime";
var TITLE_BAR_ICON_OPTIONS = [
  "none",
  "control-panel",
  "site",
  "users",
  "roles",
  "permissions",
  "hosts",
  "sites",
  "settings",
  "themes",
  "folder"
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

// src/admin/bricks/FloatingModal/FloatingModal.tsx
import {
  useEffect as useEffect3,
  useLayoutEffect,
  useRef as useRef3,
  useState
} from "react";

// src/admin/shell/geometry.ts
var DRAG_THRESHOLD_PX = 4;
var TASKBAR_RESERVE_PX = 40;
function getDesktopWorkSize(dashboard) {
  const width = dashboard.clientWidth;
  const toolbar = dashboard.querySelector("#toolbar");
  if (toolbar instanceof HTMLElement) {
    const dashboardTop = dashboard.getBoundingClientRect().top;
    const toolbarTop = toolbar.getBoundingClientRect().top;
    const height = Math.max(0, Math.floor(toolbarTop - dashboardTop));
    return { width, height };
  }
  return {
    width,
    height: Math.max(0, dashboard.clientHeight - TASKBAR_RESERVE_PX)
  };
}
function clampDesktopPosition(dashboard, width, height, left, top) {
  const work = getDesktopWorkSize(dashboard);
  const maxLeft = Math.max(0, work.width - width);
  const maxTop = Math.max(0, work.height - height);
  return {
    left: Math.max(0, Math.min(left, maxLeft)),
    top: Math.max(0, Math.min(top, maxTop))
  };
}
function findShellProductWindow(host) {
  const direct = host.querySelector(":scope > .window");
  if (direct instanceof HTMLElement) {
    return direct;
  }
  const nested = host.querySelector(":scope > .site-file-explorer > .window");
  return nested instanceof HTMLElement ? nested : null;
}
function findShellTitleBar(host) {
  const win = findShellProductWindow(host);
  const titleBar = win?.querySelector(":scope > .title-bar");
  return titleBar instanceof HTMLElement ? titleBar : null;
}

// src/admin/bricks/FloatingModal/FloatingModal.tsx
import { jsx as jsx31 } from "react/jsx-runtime";
function findOuterTitleBar(host) {
  const win = host.querySelector(":scope > .window");
  if (!(win instanceof HTMLElement)) {
    return null;
  }
  const titleBar = win.querySelector(":scope > .title-bar");
  return titleBar instanceof HTMLElement ? titleBar : null;
}
function workSize(bounds) {
  if (bounds.classList.contains("dashboard") && bounds.querySelector("#toolbar")) {
    return getDesktopWorkSize(bounds);
  }
  return { width: bounds.clientWidth, height: bounds.clientHeight };
}
function clampModalPosition(bounds, width, height, left, top) {
  if (bounds.classList.contains("dashboard") && bounds.querySelector("#toolbar")) {
    return clampDesktopPosition(bounds, width, height, left, top);
  }
  const maxLeft = Math.max(0, bounds.clientWidth - width);
  const maxTop = Math.max(0, bounds.clientHeight - height);
  return {
    left: Math.max(0, Math.min(left, maxLeft)),
    top: Math.max(0, Math.min(top, maxTop))
  };
}
function assignRef(ref, node) {
  if (!ref) {
    return;
  }
  if (typeof ref === "function") {
    ref(node);
    return;
  }
  ref.current = node;
}
function FloatingModal({
  children,
  className,
  style,
  boundsEl,
  rootRef,
  "data-owner-window": dataOwnerWindow
}) {
  const nodeRef = useRef3(null);
  const dragRef = useRef3(null);
  const [pos, setPos] = useState(null);
  const setRoot = (node) => {
    nodeRef.current = node;
    assignRef(rootRef, node);
  };
  useLayoutEffect(() => {
    const root = nodeRef.current;
    const bounds = boundsEl ?? root?.parentElement;
    if (!root || !bounds) {
      return;
    }
    const width = root.offsetWidth;
    const height = root.offsetHeight;
    const work = workSize(bounds);
    const left = Math.max(0, Math.floor((work.width - width) / 2));
    const top = Math.max(0, Math.floor((work.height - height) / 2));
    setPos(clampModalPosition(bounds, width, height, left, top));
  }, [boundsEl]);
  useEffect3(() => {
    const onMove = (event) => {
      const session = dragRef.current;
      const root = nodeRef.current;
      const bounds = boundsEl ?? root?.parentElement;
      if (!session || session.pointerId !== event.pointerId || !root || !bounds) {
        return;
      }
      const dx = event.clientX - session.startX;
      const dy = event.clientY - session.startY;
      if (!session.active) {
        if (Math.hypot(dx, dy) < DRAG_THRESHOLD_PX) {
          return;
        }
        session.active = true;
      }
      setPos(
        clampModalPosition(
          bounds,
          root.offsetWidth,
          root.offsetHeight,
          session.originLeft + dx,
          session.originTop + dy
        )
      );
    };
    const onUp = (event) => {
      const session = dragRef.current;
      if (!session || session.pointerId !== event.pointerId) {
        return;
      }
      dragRef.current = null;
      const root = nodeRef.current;
      if (root && typeof root.releasePointerCapture === "function") {
        try {
          root.releasePointerCapture(event.pointerId);
        } catch {
        }
      }
    };
    window.addEventListener("pointermove", onMove);
    window.addEventListener("pointerup", onUp);
    window.addEventListener("pointercancel", onUp);
    return () => {
      window.removeEventListener("pointermove", onMove);
      window.removeEventListener("pointerup", onUp);
      window.removeEventListener("pointercancel", onUp);
    };
  }, [boundsEl]);
  const handlePointerDown = (event) => {
    if (event.button !== 0 || !nodeRef.current || pos == null) {
      return;
    }
    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }
    if (target.closest(".title-bar-controls")) {
      return;
    }
    const titleBar = findOuterTitleBar(nodeRef.current);
    if (!titleBar || !titleBar.contains(target)) {
      return;
    }
    event.preventDefault();
    dragRef.current = {
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      originLeft: pos.left,
      originTop: pos.top,
      active: false
    };
    try {
      nodeRef.current.setPointerCapture(event.pointerId);
    } catch {
    }
  };
  const mergedStyle = {
    ...style,
    ...pos ? { left: pos.left, top: pos.top, visibility: "visible" } : { visibility: "hidden" }
  };
  return /* @__PURE__ */ jsx31(
    "div",
    {
      ref: setRoot,
      className: cn("floating-modal", className),
      style: mergedStyle,
      "data-owner-window": dataOwnerWindow,
      onPointerDown: handlePointerDown,
      children
    }
  );
}

// src/admin/bricks/DesktopModal/DesktopModal.tsx
import {
  createContext,
  useCallback,
  useContext,
  useLayoutEffect as useLayoutEffect2,
  useMemo,
  useState as useState2
} from "react";
import { createPortal } from "react-dom";

// src/admin/lib/assetPaths.ts
var ADMIN_ASSETS_BASE = "/assets/admin";
function adminAsset(path) {
  const normalized = path.replace(/^\/+/, "");
  return `${ADMIN_ASSETS_BASE}/${normalized}`;
}
function adminIconAsset(kind) {
  const file = kind.replace(/-/g, "_");
  return adminAsset(`icons/system/${file}.svg`);
}

// src/admin/lib/playAdminSound.ts
var SOUND_PATHS = {
  /** Critical Stop — error / MessageDialog open. */
  chord: "sounds/chord.mp3",
  /** Default Beep — click blocked owner while a modal is open (MessageBeep/MB_OK). */
  ding: "sounds/ding.mp3"
};
function playAdminSound(name, soundUrl) {
  if (typeof Audio === "undefined") {
    return;
  }
  const src = soundUrl || adminAsset(SOUND_PATHS[name]);
  try {
    const audio = new Audio(src);
    void audio.play().catch(() => {
    });
  } catch {
  }
}

// src/admin/lib/flashOwnedModalAttention.ts
var OWNED_MODAL_FLASH_COUNT = 3;
var OWNED_MODAL_FLASH_INTERVAL_MS = 120;
var flashTimers = /* @__PURE__ */ new WeakMap();
function findOuterTitleBar2(modalHost) {
  const direct = modalHost.querySelector(":scope > .window > .title-bar");
  if (direct instanceof HTMLElement) {
    return direct;
  }
  const nested = modalHost.querySelector(".title-bar");
  return nested instanceof HTMLElement ? nested : null;
}
function escapeAttr(value) {
  if (typeof CSS !== "undefined" && typeof CSS.escape === "function") {
    return CSS.escape(value);
  }
  return value.replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}
function findTopOwnedFloatingModal(dashboard, ownerWindowId2) {
  if (!dashboard) {
    return null;
  }
  const selector = ownerWindowId2 ? `.floating-modal.desktop-owned-modal[data-owner-window="${escapeAttr(ownerWindowId2)}"]` : ".floating-modal.desktop-owned-modal";
  const nodes = dashboard.querySelectorAll(selector);
  const last = nodes[nodes.length - 1];
  return last instanceof HTMLElement ? last : null;
}
function findTopFloatingModal(dashboard) {
  return findTopOwnedFloatingModal(dashboard);
}
function flashOwnedModalAttention(modalHost, options = {}) {
  if (!modalHost) {
    return;
  }
  const titleBar = findOuterTitleBar2(modalHost);
  if (!titleBar) {
    return;
  }
  const prev = flashTimers.get(titleBar);
  if (prev != null) {
    window.clearTimeout(prev);
    flashTimers.delete(titleBar);
  }
  if (options.playSound !== false) {
    playAdminSound("ding", options.dingSoundUrl);
  }
  const flashCount = options.flashCount ?? OWNED_MODAL_FLASH_COUNT;
  const intervalMs = options.intervalMs ?? OWNED_MODAL_FLASH_INTERVAL_MS;
  const totalSteps = Math.max(1, flashCount) * 2;
  let step = 0;
  const finish = () => {
    titleBar.classList.remove("inactive");
    flashTimers.delete(titleBar);
  };
  const tick = () => {
    const inactive = step % 2 === 0;
    titleBar.classList.toggle("inactive", inactive);
    step += 1;
    if (step >= totalSteps) {
      finish();
      return;
    }
    const id = window.setTimeout(tick, intervalMs);
    flashTimers.set(titleBar, id);
  };
  tick();
}

// src/admin/bricks/DesktopModal/DesktopModal.tsx
import { Fragment as Fragment4, jsx as jsx32, jsxs as jsxs11 } from "react/jsx-runtime";
var DesktopModalContext = createContext(null);
function findDashboard(from) {
  const closest = from?.closest(".dashboard");
  if (closest instanceof HTMLElement) {
    return closest;
  }
  const el2 = document.querySelector(".dashboard");
  return el2 instanceof HTMLElement ? el2 : null;
}
function findOwnerShellWindow(from) {
  if (!from) {
    return null;
  }
  const host = from.closest("[data-shell-window], .desktop-window");
  return host instanceof HTMLElement ? host : null;
}
function ownerWindowId(owner) {
  if (!owner) {
    return null;
  }
  return owner.getAttribute("data-shell-window") || owner.id || null;
}
function readElementZIndex(el2) {
  const inline = el2.style.zIndex;
  if (inline !== "") {
    const parsed = Number.parseInt(inline, 10);
    if (!Number.isNaN(parsed)) {
      return parsed;
    }
  }
  const computed = Number.parseInt(getComputedStyle(el2).zIndex, 10);
  return Number.isNaN(computed) ? 0 : computed;
}
function findHostBlockTarget(anchor) {
  if (!anchor) {
    return null;
  }
  const host = anchor.closest(
    ".sites-window, .site-file-explorer, .login-host, [data-shell-window], .desktop-window"
  );
  return host instanceof HTMLElement ? host : null;
}
function escapeAttr2(value) {
  if (typeof CSS !== "undefined" && typeof CSS.escape === "function") {
    return CSS.escape(value);
  }
  return value.replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}
function findTopDefaultOwnedModal(dashboard, ownerId) {
  const selector = ownerId ? `.floating-modal.desktop-owned-modal[data-owner-window="${escapeAttr2(ownerId)}"]:not(.is-alert)` : ".floating-modal.desktop-owned-modal:not(.is-alert)";
  const nodes = dashboard.querySelectorAll(selector);
  const last = nodes[nodes.length - 1];
  return last instanceof HTMLElement ? last : null;
}
function DesktopModal({
  children,
  layer = "default",
  className,
  dingSoundUrl
}) {
  const parentModal = useContext(DesktopModalContext);
  const [anchor, setAnchor] = useState2(null);
  const [dashboard, setDashboard] = useState2(null);
  const [blockTarget, setBlockTarget] = useState2(null);
  const [floatingRoot, setFloatingRoot] = useState2(null);
  const [modalZIndex, setModalZIndex] = useState2(null);
  const [ownerId, setOwnerId] = useState2(null);
  useLayoutEffect2(() => {
    const desk = findDashboard(anchor);
    const owner = findOwnerShellWindow(anchor);
    const id = ownerWindowId(owner);
    setDashboard(desk);
    setOwnerId(id);
    if (parentModal?.floatingRoot) {
      setBlockTarget(parentModal.floatingRoot);
      return;
    }
    if (layer === "alert" && desk) {
      const previous = findTopDefaultOwnedModal(desk, id);
      if (previous) {
        setBlockTarget(previous);
        return;
      }
    }
    setBlockTarget(findHostBlockTarget(anchor));
  }, [parentModal, layer, anchor, floatingRoot]);
  useLayoutEffect2(() => {
    const owner = findOwnerShellWindow(anchor);
    if (!owner) {
      setModalZIndex(null);
      return;
    }
    const sync = () => {
      setModalZIndex(readElementZIndex(owner));
    };
    sync();
    const observer = new MutationObserver(sync);
    observer.observe(owner, { attributes: true, attributeFilter: ["style", "class"] });
    return () => observer.disconnect();
  }, [anchor, layer]);
  const contextValue = useMemo(
    () => ({ floatingRoot }),
    [floatingRoot]
  );
  const onBlockedPointerDown = useCallback(
    (event) => {
      if (event.button !== 0) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      const desk = dashboard ?? findDashboard(anchor);
      const top = findTopOwnedFloatingModal(desk, ownerId) ?? floatingRoot;
      flashOwnedModalAttention(top, { dingSoundUrl });
    },
    [anchor, dashboard, dingSoundUrl, floatingRoot, ownerId]
  );
  const modalClassName = cn(
    "desktop-owned-modal",
    layer === "alert" && "is-alert",
    className
  );
  const modal = /* @__PURE__ */ jsx32(DesktopModalContext.Provider, { value: contextValue, children: /* @__PURE__ */ jsx32(
    FloatingModal,
    {
      boundsEl: dashboard,
      rootRef: setFloatingRoot,
      className: modalClassName,
      style: modalZIndex != null ? { zIndex: modalZIndex } : void 0,
      "data-owner-window": ownerId ?? void 0,
      children
    }
  ) });
  const blocker = /* @__PURE__ */ jsx32(
    "div",
    {
      className: "modal-blocker",
      "aria-hidden": true,
      onPointerDown: onBlockedPointerDown
    }
  );
  if (dashboard == null) {
    return /* @__PURE__ */ jsxs11(Fragment4, { children: [
      /* @__PURE__ */ jsx32("span", { ref: setAnchor, className: "desktop-modal-anchor", hidden: true }),
      /* @__PURE__ */ jsxs11(
        "div",
        {
          className: cn(
            "desktop-modal-layer",
            "is-local",
            layer === "alert" && "is-alert"
          ),
          children: [
            blocker,
            modal
          ]
        }
      )
    ] });
  }
  return /* @__PURE__ */ jsxs11(Fragment4, { children: [
    /* @__PURE__ */ jsx32("span", { ref: setAnchor, className: "desktop-modal-anchor", hidden: true }),
    blockTarget != null ? createPortal(blocker, blockTarget) : null,
    createPortal(modal, dashboard)
  ] });
}

// src/admin/bricks/MessageDialog/MessageDialog.tsx
import { Fragment as Fragment5, jsx as jsx33, jsxs as jsxs12 } from "react/jsx-runtime";
var DEFAULT_TITLES = {
  info: "WebHemi",
  question: "Confirm",
  warning: "Warning",
  error: "Error"
};
function MessageDialog({
  type = "error",
  title,
  message,
  onClose,
  onConfirm,
  className,
  okLabel = "OK",
  confirmLabel = "Yes",
  cancelLabel = "No"
}) {
  const isConfirm = typeof onConfirm === "function";
  return /* @__PURE__ */ jsx33(
    DialogWindow,
    {
      className: cn("message-dialog", className),
      type,
      title: title ?? DEFAULT_TITLES[type],
      titleBarControls: /* @__PURE__ */ jsx33(TitleBarControls, { children: /* @__PURE__ */ jsx33(TitleBarControl, { action: "Close", onClick: onClose }) }),
      actions: /* @__PURE__ */ jsx33(FieldRow, { className: "justify-center", children: isConfirm ? /* @__PURE__ */ jsxs12(Fragment5, { children: [
        /* @__PURE__ */ jsx33(Button2, { type: "button", isDefault: true, accessKey: "y", onClick: onConfirm, children: confirmLabel }),
        /* @__PURE__ */ jsx33(Button2, { type: "button", accessKey: "n", onClick: onClose, children: cancelLabel })
      ] }) : /* @__PURE__ */ jsx33(Button2, { type: "button", isDefault: true, accessKey: "o", onClick: onClose, children: okLabel }) }),
      children: message.split("\n").map((line, index) => /* @__PURE__ */ jsx33("p", { style: { marginTop: 0, marginBottom: 8 }, children: line }, `${index}-${line}`))
    }
  );
}

// src/admin/bricks/IconPanelWindow/IconPanelWindow.tsx
import { jsx as jsx34, jsxs as jsxs13 } from "react/jsx-runtime";
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
  return /* @__PURE__ */ jsx34(
    PaneWindowShell,
    {
      className: cn("w-window-xl", className),
      resizable,
      ...shell,
      children: /* @__PURE__ */ jsxs13(FieldBorder, { scrollable: true, className: "window-pane icon-panel-layout", style: paneStyle, children: [
        /* @__PURE__ */ jsx34("div", { className: cn("panel info", infoUnselected && "unselected"), children: info }),
        /* @__PURE__ */ jsx34("div", { className: "panel icon-list", children })
      ] })
    }
  );
}

// src/admin/bricks/IconPanelWindow/IconPanelSelectionInfo.tsx
import { Fragment as Fragment6, jsx as jsx35, jsxs as jsxs14 } from "react/jsx-runtime";
function IconPanelSelectionInfo({
  kind,
  label,
  description
}) {
  return /* @__PURE__ */ jsxs14(Fragment6, { children: [
    /* @__PURE__ */ jsx35("span", { className: cn("info-icon", kind), "aria-hidden": true }),
    /* @__PURE__ */ jsx35("h1", { className: "info-title", children: label }),
    /* @__PURE__ */ jsx35("hr", { className: "info-separator" }),
    /* @__PURE__ */ jsx35("p", { className: "info-description", children: description })
  ] });
}

// src/admin/bricks/WizardWindow/WizardWindow.tsx
import { jsx as jsx36, jsxs as jsxs15 } from "react/jsx-runtime";
function WizardWindow({
  banner,
  info,
  actions,
  className,
  ...shell
}) {
  return /* @__PURE__ */ jsx36(PaneWindowShell, { className: cn("w-window-md", className), ...shell, children: /* @__PURE__ */ jsxs15("div", { className: "window-pane wizard-panel-layout", children: [
    /* @__PURE__ */ jsx36("div", { className: "panel banner", children: banner }),
    /* @__PURE__ */ jsx36("div", { className: "panel info", children: info }),
    /* @__PURE__ */ jsx36("div", { className: "panel actions", children: actions })
  ] }) });
}

// src/admin/bricks/HeadingPanelWindow/HeadingPanelWindow.tsx
import { jsx as jsx37, jsxs as jsxs16 } from "react/jsx-runtime";
function HeadingPanelWindow({
  heading,
  children,
  actions,
  className,
  ...shell
}) {
  return /* @__PURE__ */ jsx37(PaneWindowShell, { className: cn(className), ...shell, children: /* @__PURE__ */ jsxs16("div", { className: "window-pane heading-panel-layout", children: [
    heading != null ? /* @__PURE__ */ jsx37("div", { className: "panel", children: heading }) : null,
    /* @__PURE__ */ jsx37("div", { className: "panel", children }),
    actions != null ? /* @__PURE__ */ jsx37("div", { className: "panel actions", children: actions }) : null
  ] }) });
}

// src/admin/bricks/FileExplorerWindow/FileExplorerWindow.tsx
import { useEffect as useEffect5, useMemo as useMemo2, useState as useState6 } from "react";

// src/admin/bricks/FileExplorerWindow/ExplorerContent.tsx
import { useState as useState3 } from "react";

// src/admin/bricks/FileExplorerWindow/explorerDnd.ts
var EXPLORER_DND_MIME = "application/x-webhemi-explorer-ids";
var activeDragIds = [];
function beginExplorerDrag(ids, dataTransfer) {
  activeDragIds = [...ids];
  if (!dataTransfer) {
    return;
  }
  try {
    dataTransfer.effectAllowed = "move";
    dataTransfer.setData(EXPLORER_DND_MIME, JSON.stringify(ids));
    dataTransfer.setData("text/plain", ids.join("\n"));
  } catch {
  }
}
function endExplorerDrag() {
  activeDragIds = [];
}
function readExplorerDragIds(event) {
  const data = event.dataTransfer;
  if (!data) {
    return [...activeDragIds];
  }
  try {
    const fromMime = parseJsonIds(data.getData(EXPLORER_DND_MIME));
    if (fromMime.length > 0) {
      return fromMime;
    }
    const fromText = data.getData("text/plain").split("\n").map((id) => id.trim()).filter(Boolean);
    if (fromText.length > 0) {
      return fromText;
    }
  } catch {
  }
  return [...activeDragIds];
}
function parseJsonIds(raw) {
  if (!raw) {
    return [];
  }
  try {
    const parsed = JSON.parse(raw);
    return Array.isArray(parsed) ? parsed.filter((id) => typeof id === "string") : [];
  } catch {
    return [];
  }
}

// src/admin/bricks/FileExplorerWindow/types.ts
function formatExplorerSize(sizeBytes) {
  if (sizeBytes === void 0) {
    return "";
  }
  if (sizeBytes < 1024) {
    return `${sizeBytes}B`;
  }
  return `${Math.max(1, Math.round(sizeBytes / 1024))}KB`;
}
function isExplorerFolder(item) {
  if (item.role === "folder") {
    return true;
  }
  return item.kind === "folder" || item.kind === "folder-open" || item.kind === "folder-documents" || item.kind === "folder-gallery";
}
function isExplorerDocument(item) {
  return item.role === "document" || item.kind === "file-document" || item.kind === "file-draft";
}
function isExplorerLocation(item) {
  if (item.disabled) {
    return false;
  }
  if (item.role === "site" || item.role === "media-library" || item.role === "trash") {
    return true;
  }
  return isExplorerFolder(item);
}
function isExplorerTreeExpandable(item) {
  return explorerTreeChildren(item).length > 0;
}
function explorerTreeChildren(item) {
  if (item.expandable === false || item.disabled) {
    return [];
  }
  return (item.children ?? []).filter(isExplorerFolder);
}
function explorerContentItems(selected) {
  if (!selected || selected.disabled) {
    return [];
  }
  return selected.children ?? [];
}
function findExplorerItem(roots, id) {
  if (!id) {
    return null;
  }
  for (const node of roots) {
    if (node.id === id) {
      return node;
    }
    const nested = findExplorerItem(node.children ?? [], id);
    if (nested) {
      return nested;
    }
  }
  return null;
}
function findExplorerParent(roots, id) {
  if (!id) {
    return null;
  }
  for (const node of roots) {
    if (node.id === id) {
      return null;
    }
    const kids = node.children ?? [];
    if (kids.some((child) => child.id === id)) {
      return node;
    }
    const nested = findExplorerParent(kids, id);
    if (nested) {
      return nested;
    }
  }
  return null;
}
function findExplorerAncestorIds(roots, id) {
  if (!id) {
    return [];
  }
  const walk = (nodes, path) => {
    for (const node of nodes) {
      if (node.id === id) {
        return path;
      }
      const found = walk(node.children ?? [], [...path, node.id]);
      if (found) {
        return found;
      }
    }
    return null;
  };
  return walk(roots, []) ?? [];
}

// src/admin/bricks/FileExplorerWindow/ExplorerContent.tsx
import { jsx as jsx38, jsxs as jsxs17 } from "react/jsx-runtime";
function Glyph({ kind }) {
  return /* @__PURE__ */ jsx38("span", { className: cn("explorer-glyph", kind), "aria-hidden": true });
}
function modifiersFromEvent(event) {
  return {
    ctrlKey: event.ctrlKey,
    metaKey: event.metaKey,
    shiftKey: event.shiftKey
  };
}
function ExplorerContent({
  view,
  items,
  selectedIds = [],
  cutItemIds = [],
  onSelect,
  onOpen,
  onItemsDrop
}) {
  const selected = new Set(selectedIds);
  const cut = new Set(cutItemIds);
  const [dragOverId, setDragOverId] = useState3(null);
  const openItem = (item) => {
    if (isExplorerDocument(item)) {
      return;
    }
    onOpen?.(item);
  };
  const selectFromMouse = (item, event) => {
    onSelect?.(item, modifiersFromEvent(event));
  };
  const onDragStartItem = (item, event) => {
    if (!onItemsDrop) {
      event.preventDefault();
      return;
    }
    const ids = selected.has(item.id) ? selectedIds : [item.id];
    beginExplorerDrag(ids, event.dataTransfer);
    if (!selected.has(item.id)) {
      onSelect?.(item, { ctrlKey: false, metaKey: false, shiftKey: false });
    }
  };
  const canDropOn = (item) => isExplorerLocation(item) && !item.disabled;
  const onDragOverItem = (item, event) => {
    if (!onItemsDrop || !canDropOn(item)) {
      return;
    }
    event.preventDefault();
    try {
      event.dataTransfer.dropEffect = "move";
    } catch {
    }
    if (dragOverId !== item.id) {
      setDragOverId(item.id);
    }
  };
  const onDragLeaveItem = (item) => {
    if (dragOverId === item.id) {
      setDragOverId(null);
    }
  };
  const onDropItem = (item, event) => {
    if (!onItemsDrop || !canDropOn(item)) {
      return;
    }
    event.preventDefault();
    setDragOverId(null);
    const ids = readExplorerDragIds(event).filter((id) => id !== item.id);
    endExplorerDrag();
    if (ids.length === 0) {
      return;
    }
    onItemsDrop(ids, item.id);
  };
  const itemClass = (item) => cn(
    item.hidden && "is-hidden",
    selected.has(item.id) && "is-selected",
    cut.has(item.id) && "is-cut",
    dragOverId === item.id && "is-drag-over"
  );
  if (view === "large-icons") {
    return /* @__PURE__ */ jsx38("div", { className: "explorer-content-inner large-icons", children: items.map((item) => /* @__PURE__ */ jsx38(
      SystemIcon,
      {
        kind: item.kind,
        label: item.label,
        labelTone: "dark",
        draggable: Boolean(onItemsDrop),
        className: itemClass(item),
        onActivate: (event) => selectFromMouse(item, event),
        onOpen: () => openItem(item),
        onDragStart: (event) => onDragStartItem(item, event),
        onDragOver: (event) => onDragOverItem(item, event),
        onDragLeave: () => onDragLeaveItem(item),
        onDrop: (event) => onDropItem(item, event)
      },
      item.id
    )) });
  }
  if (view === "list") {
    return /* @__PURE__ */ jsx38("div", { className: "explorer-content-inner list", children: items.map((item) => /* @__PURE__ */ jsxs17(
      "a",
      {
        href: "#",
        draggable: Boolean(onItemsDrop),
        className: cn("explorer-list-item", itemClass(item)),
        onClick: (event) => {
          event.preventDefault();
          selectFromMouse(item, event);
        },
        onDoubleClick: (event) => {
          event.preventDefault();
          openItem(item);
        },
        onDragStart: (event) => onDragStartItem(item, event),
        onDragOver: (event) => onDragOverItem(item, event),
        onDragLeave: () => onDragLeaveItem(item),
        onDrop: (event) => onDropItem(item, event),
        children: [
          /* @__PURE__ */ jsx38(Glyph, { kind: item.kind }),
          /* @__PURE__ */ jsx38("span", { className: "label", children: item.label })
        ]
      },
      item.id
    )) });
  }
  return /* @__PURE__ */ jsx38("div", { className: "explorer-content-inner details", children: /* @__PURE__ */ jsxs17(Table, { className: "explorer-details", children: [
    /* @__PURE__ */ jsx38("thead", { children: /* @__PURE__ */ jsxs17("tr", { children: [
      /* @__PURE__ */ jsx38("th", { children: "Name" }),
      /* @__PURE__ */ jsx38("th", { children: "Size" }),
      /* @__PURE__ */ jsx38("th", { children: "Type" }),
      /* @__PURE__ */ jsx38("th", { children: "Modified" })
    ] }) }),
    /* @__PURE__ */ jsx38("tbody", { children: items.map((item) => /* @__PURE__ */ jsxs17(
      TableRow,
      {
        draggable: Boolean(onItemsDrop),
        highlighted: selected.has(item.id),
        className: itemClass(item),
        onClick: (event) => selectFromMouse(item, event),
        onDoubleClick: () => openItem(item),
        onDragStart: (event) => onDragStartItem(item, event),
        onDragOver: (event) => onDragOverItem(item, event),
        onDragLeave: () => onDragLeaveItem(item),
        onDrop: (event) => onDropItem(item, event),
        children: [
          /* @__PURE__ */ jsxs17("td", { className: "name-cell", children: [
            /* @__PURE__ */ jsx38(Glyph, { kind: item.kind }),
            item.label
          ] }),
          /* @__PURE__ */ jsx38("td", { children: formatExplorerSize(item.sizeBytes) }),
          /* @__PURE__ */ jsx38("td", { children: item.typeLabel ?? "" }),
          /* @__PURE__ */ jsx38("td", { children: item.modifiedAt ?? "" })
        ]
      },
      item.id
    )) })
  ] }) });
}

// src/admin/bricks/FileExplorerWindow/ExplorerMenuBar.tsx
import {
  useEffect as useEffect4,
  useId,
  useRef as useRef4,
  useState as useState4
} from "react";
import { Fragment as Fragment7, jsx as jsx39, jsxs as jsxs18 } from "react/jsx-runtime";
function MenuLabel({ text, accessKey }) {
  return /* @__PURE__ */ jsx39(Fragment7, { children: underlineAccessKey(text, accessKey) });
}
function buildMenus(props) {
  const {
    view,
    onViewChange,
    onFileOpen,
    fileOpenDisabled = false,
    onNewFolder,
    onNewPage,
    onRename,
    onDelete,
    onProperties,
    onClose,
    onUndo,
    onCut,
    onCopy,
    onPaste,
    onSelectAll,
    onRefresh,
    statusBarVisible = true,
    onStatusBarToggle,
    onAbout
  } = props;
  return {
    file: [
      {
        kind: "item",
        id: "new-folder",
        label: "New Folder",
        accessKey: "F",
        disabled: !onNewFolder,
        onSelect: onNewFolder
      },
      {
        kind: "item",
        id: "new-page",
        label: "New Page",
        accessKey: "N",
        disabled: !onNewPage,
        onSelect: onNewPage
      },
      { kind: "separator", id: "file-sep-1" },
      {
        kind: "item",
        id: "open",
        label: "Open",
        accessKey: "O",
        disabled: !onFileOpen || fileOpenDisabled,
        onSelect: onFileOpen
      },
      {
        kind: "item",
        id: "rename",
        label: "Rename",
        accessKey: "M",
        disabled: !onRename,
        onSelect: onRename
      },
      {
        kind: "item",
        id: "delete",
        label: "Delete",
        accessKey: "D",
        disabled: !onDelete,
        onSelect: onDelete
      },
      {
        kind: "item",
        id: "properties",
        label: "Properties",
        accessKey: "R",
        disabled: !onProperties,
        onSelect: onProperties
      },
      { kind: "separator", id: "file-sep-2" },
      {
        kind: "item",
        id: "close",
        label: "Close",
        accessKey: "C",
        disabled: !onClose,
        onSelect: onClose
      }
    ],
    edit: [
      {
        kind: "item",
        id: "undo",
        label: "Undo",
        accessKey: "U",
        disabled: !onUndo,
        onSelect: onUndo
      },
      { kind: "separator", id: "edit-sep-1" },
      {
        kind: "item",
        id: "cut",
        label: "Cut",
        accessKey: "T",
        disabled: !onCut,
        onSelect: onCut
      },
      {
        kind: "item",
        id: "copy",
        label: "Copy",
        accessKey: "C",
        disabled: !onCopy,
        onSelect: onCopy
      },
      {
        kind: "item",
        id: "paste",
        label: "Paste",
        accessKey: "P",
        disabled: !onPaste,
        onSelect: onPaste
      },
      { kind: "separator", id: "edit-sep-2" },
      {
        kind: "item",
        id: "select-all",
        label: "Select All",
        accessKey: "A",
        disabled: !onSelectAll,
        onSelect: onSelectAll
      }
    ],
    view: [
      {
        kind: "item",
        id: "large-icons",
        label: "Large Icons",
        accessKey: "G",
        role: "menuitemradio",
        checked: view === "large-icons",
        disabled: !onViewChange,
        onSelect: () => onViewChange?.("large-icons")
      },
      {
        kind: "item",
        id: "list",
        label: "List",
        accessKey: "L",
        role: "menuitemradio",
        checked: view === "list",
        disabled: !onViewChange,
        onSelect: () => onViewChange?.("list")
      },
      {
        kind: "item",
        id: "details",
        label: "Details",
        accessKey: "D",
        role: "menuitemradio",
        checked: view === "details",
        disabled: !onViewChange,
        onSelect: () => onViewChange?.("details")
      },
      { kind: "separator", id: "view-sep-1" },
      {
        kind: "item",
        id: "refresh",
        label: "Refresh",
        accessKey: "R",
        disabled: !onRefresh,
        onSelect: onRefresh
      },
      {
        kind: "item",
        id: "status-bar",
        label: "Status Bar",
        accessKey: "B",
        role: "menuitemcheckbox",
        checked: statusBarVisible,
        disabled: !onStatusBarToggle,
        onSelect: onStatusBarToggle
      }
    ],
    help: [
      {
        kind: "item",
        id: "about",
        label: "About File Explorer\u2026",
        accessKey: "A",
        disabled: !onAbout,
        onSelect: onAbout
      }
    ]
  };
}
var TOP_LEVEL = [
  { id: "file", label: "File", accessKey: "F" },
  { id: "edit", label: "Edit", accessKey: "E" },
  { id: "view", label: "View", accessKey: "V" },
  { id: "help", label: "Help", accessKey: "H" }
];
function ExplorerMenuBar(props) {
  const { className } = props;
  const menus = buildMenus(props);
  const rootRef = useRef4(null);
  const [openMenu, setOpenMenu] = useState4(null);
  const baseId = useId();
  useEffect4(() => {
    if (!openMenu) {
      return;
    }
    const onPointerDown = (event) => {
      if (!rootRef.current?.contains(event.target)) {
        setOpenMenu(null);
      }
    };
    const onKeyDown = (event) => {
      if (event.key === "Escape") {
        setOpenMenu(null);
      }
    };
    document.addEventListener("pointerdown", onPointerDown);
    document.addEventListener("keydown", onKeyDown);
    return () => {
      document.removeEventListener("pointerdown", onPointerDown);
      document.removeEventListener("keydown", onKeyDown);
    };
  }, [openMenu]);
  const activateItem = (item) => {
    if (item.disabled || !item.onSelect) {
      return;
    }
    item.onSelect();
    setOpenMenu(null);
  };
  const onMenuKeyDown = (event, menuId) => {
    if (event.key === "ArrowRight" || event.key === "ArrowLeft") {
      event.preventDefault();
      const index = TOP_LEVEL.findIndex((entry) => entry.id === menuId);
      const delta = event.key === "ArrowRight" ? 1 : -1;
      const next = TOP_LEVEL[(index + delta + TOP_LEVEL.length) % TOP_LEVEL.length];
      setOpenMenu(next.id);
    }
  };
  return /* @__PURE__ */ jsx39(
    "div",
    {
      ref: rootRef,
      className: cn("panel explorer-menubar", className),
      role: "menubar",
      "aria-label": "Explorer",
      children: TOP_LEVEL.map((top) => {
        const menuId = `${baseId}-${top.id}`;
        const expanded = openMenu === top.id;
        const items = menus[top.id];
        return /* @__PURE__ */ jsxs18(
          "div",
          {
            className: "explorer-menu-root",
            onKeyDown: (event) => onMenuKeyDown(event, top.id),
            onMouseEnter: () => {
              if (openMenu !== null) {
                setOpenMenu(top.id);
              }
            },
            children: [
              /* @__PURE__ */ jsx39(
                "button",
                {
                  type: "button",
                  className: "explorer-menu-button",
                  role: "menuitem",
                  "aria-haspopup": "true",
                  "aria-expanded": expanded,
                  "aria-controls": menuId,
                  onClick: () => {
                    setOpenMenu((current) => current === top.id ? null : top.id);
                  },
                  children: /* @__PURE__ */ jsx39(MenuLabel, { text: top.label, accessKey: top.accessKey })
                }
              ),
              expanded ? /* @__PURE__ */ jsx39("div", { id: menuId, className: "explorer-menu", role: "menu", "aria-label": top.label, children: items.map((item) => {
                if (item.kind === "separator") {
                  return /* @__PURE__ */ jsx39("div", { className: "explorer-menu-separator", role: "separator" }, item.id);
                }
                const role = item.role ?? "menuitem";
                return /* @__PURE__ */ jsxs18(
                  "button",
                  {
                    type: "button",
                    role,
                    className: cn(
                      "explorer-menu-item",
                      item.checked && "is-checked",
                      item.disabled && "is-disabled"
                    ),
                    disabled: item.disabled,
                    "aria-checked": role === "menuitemradio" || role === "menuitemcheckbox" ? item.checked : void 0,
                    onClick: () => activateItem(item),
                    children: [
                      /* @__PURE__ */ jsx39("span", { className: "explorer-menu-check", "aria-hidden": true, children: item.checked ? "\u2713" : "" }),
                      /* @__PURE__ */ jsx39("span", { className: "explorer-menu-label", children: /* @__PURE__ */ jsx39(MenuLabel, { text: item.label, accessKey: item.accessKey }) })
                    ]
                  },
                  item.id
                );
              }) }) : null
            ]
          },
          top.id
        );
      })
    }
  );
}

// src/admin/bricks/FileExplorerWindow/ExplorerSplitter.tsx
import {
  useCallback as useCallback2,
  useRef as useRef5,
  useState as useState5
} from "react";
import { jsx as jsx40 } from "react/jsx-runtime";
function ExplorerSplitter({
  value,
  onChange,
  min = 120,
  max = 480,
  disabled = false,
  className,
  keyboardStep = 8
}) {
  const [dragging, setDragging] = useState5(false);
  const dragRef = useRef5(
    null
  );
  const clamp = useCallback2(
    (width) => Math.min(max, Math.max(min, Math.round(width))),
    [min, max]
  );
  const endDrag = (event) => {
    const drag = dragRef.current;
    if (!drag || event.pointerId !== drag.pointerId) {
      return;
    }
    dragRef.current = null;
    setDragging(false);
    try {
      if (event.currentTarget.hasPointerCapture?.(event.pointerId)) {
        event.currentTarget.releasePointerCapture(event.pointerId);
      }
    } catch {
    }
  };
  const onPointerDown = (event) => {
    if (disabled || event.button !== 0) {
      return;
    }
    event.preventDefault();
    dragRef.current = {
      pointerId: event.pointerId,
      startX: event.clientX,
      startWidth: value
    };
    setDragging(true);
    try {
      event.currentTarget.setPointerCapture(event.pointerId);
    } catch {
    }
  };
  const onPointerMove = (event) => {
    const drag = dragRef.current;
    if (!drag || event.pointerId !== drag.pointerId) {
      return;
    }
    onChange(clamp(drag.startWidth + (event.clientX - drag.startX)));
  };
  const onKeyDown = (event) => {
    if (disabled) {
      return;
    }
    if (event.key === "ArrowLeft") {
      event.preventDefault();
      onChange(clamp(value - keyboardStep));
    } else if (event.key === "ArrowRight") {
      event.preventDefault();
      onChange(clamp(value + keyboardStep));
    } else if (event.key === "Home") {
      event.preventDefault();
      onChange(min);
    } else if (event.key === "End") {
      event.preventDefault();
      onChange(max);
    }
  };
  return /* @__PURE__ */ jsx40(
    "div",
    {
      className: cn("explorer-splitter", dragging && "is-dragging", className),
      role: "separator",
      "aria-orientation": "vertical",
      "aria-label": "Resize tree pane",
      "aria-valuenow": value,
      "aria-valuemin": min,
      "aria-valuemax": max,
      "aria-disabled": disabled || void 0,
      tabIndex: disabled ? -1 : 0,
      onPointerDown,
      onPointerMove,
      onPointerUp: endDrag,
      onPointerCancel: endDrag,
      onKeyDown
    }
  );
}

// src/admin/bricks/FileExplorerWindow/ExplorerToolbar.tsx
import { jsx as jsx41, jsxs as jsxs19 } from "react/jsx-runtime";
var VIEW_TOOLS = [
  { view: "large-icons", label: "Large Icons", className: "large-icons" },
  { view: "list", label: "List", className: "list" },
  { view: "details", label: "Details", className: "details" }
];
function ExplorerToolbar({
  view,
  onViewChange,
  onLevelUp,
  levelUpDisabled = false,
  onCut,
  onCopy,
  onPaste,
  onUndo,
  onDelete,
  onProperties,
  className
}) {
  return /* @__PURE__ */ jsxs19("div", { className: cn("panel explorer-toolbar", className), role: "toolbar", "aria-label": "Explorer", children: [
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool level-up",
        "aria-label": "Up one level",
        disabled: levelUpDisabled,
        onClick: onLevelUp
      }
    ),
    /* @__PURE__ */ jsx41(VerticalBar, {}),
    /* @__PURE__ */ jsx41(Button2, { type: "button", className: "tool cut", "aria-label": "Cut", disabled: !onCut, onClick: onCut }),
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool copy",
        "aria-label": "Copy",
        disabled: !onCopy,
        onClick: onCopy
      }
    ),
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool paste",
        "aria-label": "Paste",
        disabled: !onPaste,
        onClick: onPaste
      }
    ),
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool undo",
        "aria-label": "Undo",
        disabled: !onUndo,
        onClick: onUndo
      }
    ),
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool delete",
        "aria-label": "Delete",
        disabled: !onDelete,
        onClick: onDelete
      }
    ),
    /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: "tool properties",
        "aria-label": "Properties",
        disabled: !onProperties,
        onClick: onProperties
      }
    ),
    /* @__PURE__ */ jsx41(VerticalBar, {}),
    VIEW_TOOLS.map((tool) => /* @__PURE__ */ jsx41(
      Button2,
      {
        type: "button",
        className: cn("tool", tool.className),
        "aria-label": tool.label,
        "aria-pressed": view === tool.view,
        onClick: () => onViewChange?.(tool.view)
      },
      tool.view
    ))
  ] });
}

// src/admin/bricks/FileExplorerWindow/FileExplorerWindow.tsx
import { jsx as jsx42, jsxs as jsxs20 } from "react/jsx-runtime";
var DEFAULT_TREE_WIDTH = 200;
var MIN_TREE_WIDTH = 120;
var MAX_TREE_WIDTH = 480;
function treeGlyphKind(node, expanded) {
  if ((node.kind === "folder" || node.role === "folder") && expanded) {
    return "folder-open";
  }
  return node.kind;
}
function TreeNodeLabel({
  node,
  expanded = false
}) {
  return /* @__PURE__ */ jsxs20("span", { className: cn("explorer-tree-node", node.disabled && "is-disabled"), children: [
    /* @__PURE__ */ jsx42("span", { className: cn("explorer-glyph", treeGlyphKind(node, expanded)), "aria-hidden": true }),
    /* @__PURE__ */ jsx42("span", { className: "tree-view-label", children: node.label })
  ] });
}
function readDraggedIds(event) {
  return readExplorerDragIds(event);
}
function TreeDropLink({
  node,
  isCurrent,
  onTreeSelect,
  onItemsDrop,
  children
}) {
  const droppable = Boolean(onItemsDrop) && isExplorerLocation(node) && !node.disabled;
  const [dragOver, setDragOver] = useState6(false);
  return /* @__PURE__ */ jsx42(
    "a",
    {
      href: "#",
      "aria-current": isCurrent ? "true" : void 0,
      className: cn(droppable && dragOver && "is-drag-over"),
      onClick: (event) => {
        event.preventDefault();
        event.stopPropagation();
        onTreeSelect?.(node);
      },
      onDragEnter: droppable ? (event) => {
        event.preventDefault();
        setDragOver(true);
      } : void 0,
      onDragLeave: droppable ? () => {
        setDragOver(false);
      } : void 0,
      onDragOver: droppable ? (event) => {
        event.preventDefault();
        try {
          event.dataTransfer.dropEffect = "move";
        } catch {
        }
      } : void 0,
      onDrop: droppable ? (event) => {
        event.preventDefault();
        event.stopPropagation();
        setDragOver(false);
        const ids = readDraggedIds(event).filter((id) => id !== node.id);
        endExplorerDrag();
        if (ids.length > 0) {
          onItemsDrop?.(ids, node.id);
        }
      } : void 0,
      children
    }
  );
}
function ExplorerTreeBranch({
  node,
  locationId,
  ancestorIds,
  onTreeSelect,
  onItemsDrop
}) {
  const kids = explorerTreeChildren(node);
  const [open, setOpen] = useState6(node.role === "site" || ancestorIds.has(node.id));
  useEffect5(() => {
    if (ancestorIds.has(node.id)) {
      setOpen(true);
    }
  }, [ancestorIds, node.id]);
  const isCurrent = locationId === node.id;
  return /* @__PURE__ */ jsx42("li", { children: /* @__PURE__ */ jsxs20("details", { open, children: [
    /* @__PURE__ */ jsxs20(
      "summary",
      {
        tabIndex: -1,
        onClick: (event) => {
          event.preventDefault();
        },
        children: [
          /* @__PURE__ */ jsx42(
            TreeToggle,
            {
              expanded: open,
              onClick: (event) => {
                event.preventDefault();
                event.stopPropagation();
                setOpen((value) => !value);
              }
            }
          ),
          node.disabled ? /* @__PURE__ */ jsx42("span", { className: "explorer-tree-leaf is-disabled", "aria-disabled": "true", children: /* @__PURE__ */ jsx42(TreeNodeLabel, { node, expanded: open }) }) : /* @__PURE__ */ jsx42(
            TreeDropLink,
            {
              node,
              isCurrent,
              onTreeSelect,
              onItemsDrop,
              children: /* @__PURE__ */ jsx42(TreeNodeLabel, { node, expanded: open })
            }
          )
        ]
      }
    ),
    /* @__PURE__ */ jsx42("ul", { children: renderTreeNodes(kids, locationId, ancestorIds, onTreeSelect, onItemsDrop) })
  ] }) });
}
function renderTreeNodes(nodes, locationId, ancestorIds, onTreeSelect, onItemsDrop) {
  return nodes.map((node) => {
    const canExpand = isExplorerTreeExpandable(node);
    const isCurrent = locationId === node.id;
    if (canExpand) {
      return /* @__PURE__ */ jsx42(
        ExplorerTreeBranch,
        {
          node,
          locationId,
          ancestorIds,
          onTreeSelect,
          onItemsDrop
        },
        node.id
      );
    }
    return /* @__PURE__ */ jsx42("li", { className: cn(node.disabled && "is-disabled"), children: node.disabled ? /* @__PURE__ */ jsx42("span", { className: "explorer-tree-leaf is-disabled", "aria-disabled": "true", children: /* @__PURE__ */ jsx42(TreeNodeLabel, { node }) }) : /* @__PURE__ */ jsx42(
      TreeDropLink,
      {
        node,
        isCurrent,
        onTreeSelect,
        onItemsDrop,
        children: /* @__PURE__ */ jsx42(TreeNodeLabel, { node })
      }
    ) }, node.id);
  });
}
function FileExplorerWindow({
  tree,
  items,
  view = "large-icons",
  onViewChange,
  locationId = null,
  selectedIds = [],
  cutItemIds = [],
  onTreeSelect,
  onSelect,
  onOpen,
  onItemsDrop,
  onLevelUp,
  levelUpDisabled = false,
  onCut,
  onCopy,
  onPaste,
  onUndo,
  onDelete,
  onProperties,
  onClose,
  onNewFolder,
  onNewPage,
  onRename,
  onSelectAll,
  onRefresh,
  statusBarVisible = true,
  onStatusBarToggle,
  onAbout,
  className,
  paneHeight = 360,
  treeWidth = DEFAULT_TREE_WIDTH,
  onTreeWidthChange,
  minTreeWidth = MIN_TREE_WIDTH,
  maxTreeWidth = MAX_TREE_WIDTH,
  treePaneResizable = true,
  resizable = true,
  ...shell
}) {
  const [uncontrolledTreeWidth, setUncontrolledTreeWidth] = useState6(treeWidth);
  const isTreeWidthControlled = onTreeWidthChange !== void 0;
  const resolvedTreeWidth = isTreeWidthControlled ? treeWidth : uncontrolledTreeWidth;
  useEffect5(() => {
    if (!isTreeWidthControlled) {
      setUncontrolledTreeWidth(treeWidth);
    }
  }, [treeWidth, isTreeWidthControlled]);
  const setTreeWidth = (width) => {
    if (isTreeWidthControlled) {
      onTreeWidthChange(width);
    } else {
      setUncontrolledTreeWidth(width);
    }
  };
  const paneStyle = {
    height: typeof paneHeight === "number" ? `${paneHeight}px` : paneHeight
  };
  const treeStyle = {
    flexBasis: `${resolvedTreeWidth}px`,
    width: `${resolvedTreeWidth}px`
  };
  const ancestorIds = useMemo2(
    () => new Set(findExplorerAncestorIds(tree, locationId)),
    [tree, locationId]
  );
  const primarySelectedId = selectedIds.length > 0 ? selectedIds[selectedIds.length - 1] : null;
  const selectedItem = findExplorerItem(tree, primarySelectedId) ?? items.find((item) => item.id === primarySelectedId) ?? null;
  return /* @__PURE__ */ jsx42(
    PaneWindowShell,
    {
      className: cn("w-window-xl file-explorer-window", className),
      resizable,
      ...shell,
      children: /* @__PURE__ */ jsxs20("div", { className: "window-pane explorer-panel-layout", style: paneStyle, children: [
        /* @__PURE__ */ jsx42(
          ExplorerMenuBar,
          {
            view,
            onViewChange,
            onFileOpen: onOpen ? () => {
              if (selectedItem) {
                onOpen(selectedItem);
              }
            } : void 0,
            fileOpenDisabled: !selectedItem,
            onNewFolder,
            onNewPage,
            onRename,
            onDelete,
            onProperties,
            onClose,
            onUndo,
            onCut,
            onCopy,
            onPaste,
            onSelectAll,
            onRefresh,
            statusBarVisible,
            onStatusBarToggle,
            onAbout
          }
        ),
        /* @__PURE__ */ jsx42("div", { className: "explorer-chrome-separator", role: "separator" }),
        /* @__PURE__ */ jsx42(
          ExplorerToolbar,
          {
            view,
            onViewChange,
            onLevelUp,
            levelUpDisabled,
            onCut,
            onCopy,
            onPaste,
            onUndo,
            onDelete,
            onProperties
          }
        ),
        /* @__PURE__ */ jsxs20("div", { className: "explorer-split", children: [
          /* @__PURE__ */ jsx42(FieldBorder, { scrollable: true, className: "panel explorer-tree", style: treeStyle, children: /* @__PURE__ */ jsx42("div", { className: "explorer-tree-inner", children: /* @__PURE__ */ jsx42(TreeView, { children: renderTreeNodes(tree, locationId, ancestorIds, onTreeSelect, onItemsDrop) }) }) }),
          treePaneResizable ? /* @__PURE__ */ jsx42(
            ExplorerSplitter,
            {
              value: resolvedTreeWidth,
              onChange: setTreeWidth,
              min: minTreeWidth,
              max: maxTreeWidth
            }
          ) : /* @__PURE__ */ jsx42("div", { className: "explorer-splitter is-static", "aria-hidden": true }),
          /* @__PURE__ */ jsx42(FieldBorder, { scrollable: true, className: "panel explorer-content", children: /* @__PURE__ */ jsx42(
            ExplorerContent,
            {
              view,
              items,
              selectedIds,
              cutItemIds,
              onSelect,
              onOpen,
              onItemsDrop
            }
          ) })
        ] })
      ] })
    }
  );
}

// src/admin/bricks/FileExplorerWindow/ExplorerPropertiesDialog.tsx
import { jsx as jsx43, jsxs as jsxs21 } from "react/jsx-runtime";
function ExplorerPropertiesDialog({
  item,
  parentLabel = null,
  onClose,
  className
}) {
  const sizeLabel = formatExplorerSize(item.sizeBytes) || "\u2014";
  return /* @__PURE__ */ jsx43(
    DialogWindow,
    {
      className: cn("explorer-properties-dialog", className),
      title: `${item.label} Properties`,
      titleBarControls: /* @__PURE__ */ jsx43(TitleBarControls, { children: /* @__PURE__ */ jsx43(TitleBarControl, { action: "Close", onClick: onClose }) }),
      actions: /* @__PURE__ */ jsxs21(FieldRow, { className: "justify-end", children: [
        /* @__PURE__ */ jsx43(Button2, { type: "button", isDefault: true, accessKey: "o", onClick: onClose, children: "OK" }),
        /* @__PURE__ */ jsx43(Button2, { type: "button", accessKey: "c", onClick: onClose, children: "Cancel" })
      ] }),
      children: /* @__PURE__ */ jsxs21("div", { className: "explorer-properties", children: [
        /* @__PURE__ */ jsxs21("div", { className: "explorer-properties-identity", children: [
          /* @__PURE__ */ jsx43("span", { className: cn("explorer-glyph", item.kind), "aria-hidden": true }),
          /* @__PURE__ */ jsx43("span", { className: "explorer-properties-name", children: item.label })
        ] }),
        /* @__PURE__ */ jsxs21("dl", { className: "explorer-properties-list", children: [
          /* @__PURE__ */ jsxs21("div", { children: [
            /* @__PURE__ */ jsx43("dt", { children: "Type:" }),
            /* @__PURE__ */ jsx43("dd", { children: item.typeLabel ?? "\u2014" })
          ] }),
          /* @__PURE__ */ jsxs21("div", { children: [
            /* @__PURE__ */ jsx43("dt", { children: "Location:" }),
            /* @__PURE__ */ jsx43("dd", { children: parentLabel ?? "\u2014" })
          ] }),
          /* @__PURE__ */ jsxs21("div", { children: [
            /* @__PURE__ */ jsx43("dt", { children: "Size:" }),
            /* @__PURE__ */ jsx43("dd", { children: sizeLabel })
          ] }),
          /* @__PURE__ */ jsxs21("div", { children: [
            /* @__PURE__ */ jsx43("dt", { children: "Modified:" }),
            /* @__PURE__ */ jsx43("dd", { children: item.modifiedAt ?? "\u2014" })
          ] })
        ] })
      ] })
    }
  );
}

// src/admin/bricks/FileExplorerWindow/SiteFileExplorer.tsx
import { useMemo as useMemo3, useState as useState7 } from "react";

// src/admin/bricks/FileExplorerWindow/explorerTreeOps.ts
function cloneExplorerForest(nodes) {
  return nodes.map((node) => ({
    ...node,
    children: node.children ? cloneExplorerForest(node.children) : void 0
  }));
}
function findExplorerTrashRoot(roots) {
  return roots.find((node) => node.role === "trash") ?? null;
}
function isUnderExplorerTrash(roots, id) {
  if (!id) {
    return false;
  }
  const trash = findExplorerTrashRoot(roots);
  if (!trash) {
    return false;
  }
  if (id === trash.id) {
    return true;
  }
  return findExplorerAncestorIds(roots, id).includes(trash.id);
}
function canDeleteExplorerItem(roots, item) {
  if (!item) {
    return false;
  }
  if (roots.some((root) => root.id === item.id)) {
    return false;
  }
  if (item.role === "site" || item.role === "media-library" || item.role === "trash" || item.role === "settings") {
    return false;
  }
  return findExplorerItem(roots, item.id) !== null;
}
function mapForest(nodes, mapNode) {
  const result = [];
  for (const node of nodes) {
    const mapped = mapNode(node);
    if (!mapped) {
      continue;
    }
    result.push(mapped);
  }
  return result;
}
function removeExplorerItem(roots, id) {
  const parent = findExplorerParent(roots, id);
  let removed = null;
  const walk = (nodes) => mapForest(nodes, (node) => {
    if (node.id === id) {
      removed = node;
      return null;
    }
    if (!node.children?.length) {
      return node;
    }
    return { ...node, children: walk(node.children) };
  });
  const tree = walk(roots);
  return { tree, removed, parentId: parent?.id ?? null };
}
function appendExplorerChild(roots, parentId, child) {
  const walk = (nodes) => nodes.map((node) => {
    if (node.id === parentId) {
      return {
        ...node,
        children: [...node.children ?? [], child]
      };
    }
    if (!node.children?.length) {
      return node;
    }
    return { ...node, children: walk(node.children) };
  });
  return walk(roots);
}
function canCutOrCopyExplorerItem(roots, item) {
  return canDeleteExplorerItem(roots, item);
}
function canCutOrCopyExplorerItems(roots, items) {
  return items.length > 0 && items.every((item) => canCutOrCopyExplorerItem(roots, item));
}
function canPasteIntoExplorerLocation(roots, locationId, clipboard) {
  if (!clipboard?.items.length || !locationId) {
    return false;
  }
  const location = findExplorerItem(roots, locationId);
  if (!location || location.disabled) {
    return false;
  }
  if (location.role === "trash" || location.role === "settings") {
    return false;
  }
  if (location.role !== "site" && location.role !== "media-library" && location.role !== "folder" && !isExplorerFolder(location)) {
    return false;
  }
  if (clipboard.mode === "cut") {
    for (const item of clipboard.items) {
      if (!findExplorerItem(roots, item.id)) {
        return false;
      }
      if (locationId === item.id) {
        return false;
      }
      if (findExplorerAncestorIds(roots, locationId).includes(item.id)) {
        return false;
      }
    }
  }
  return true;
}
function deleteExplorerItem(roots, itemId) {
  const item = findExplorerItem(roots, itemId);
  if (!canDeleteExplorerItem(roots, item) || !item) {
    return null;
  }
  const parent = findExplorerParent(roots, itemId);
  if (!parent) {
    return null;
  }
  if (isUnderExplorerTrash(roots, itemId)) {
    const { tree, removed: removed2, parentId: parentId2 } = removeExplorerItem(roots, itemId);
    if (!removed2 || !parentId2) {
      return null;
    }
    return {
      tree,
      undo: { type: "permanent", item: removed2, parentId: parentId2 }
    };
  }
  const trash = findExplorerTrashRoot(roots);
  if (!trash) {
    return null;
  }
  const { tree: without, removed, parentId } = removeExplorerItem(roots, itemId);
  if (!removed || !parentId) {
    return null;
  }
  return {
    tree: appendExplorerChild(without, trash.id, removed),
    undo: { type: "to-trash", item: removed, parentId }
  };
}
function cloneExplorerItemWithNewIds(roots, item) {
  const used = /* @__PURE__ */ new Set();
  const nextId = (oldId) => {
    let candidate = `${oldId}-copy`;
    let n = 1;
    while (findExplorerItem(roots, candidate) || used.has(candidate)) {
      n += 1;
      candidate = `${oldId}-copy-${n}`;
    }
    used.add(candidate);
    return candidate;
  };
  const walk = (node) => ({
    ...node,
    id: nextId(node.id),
    children: node.children?.map(walk)
  });
  return walk(cloneExplorerForest([item])[0]);
}
function moveExplorerItems(roots, itemIds, targetId) {
  const items = itemIds.map((id) => findExplorerItem(roots, id)).filter((item) => item !== null);
  if (!canCutOrCopyExplorerItems(roots, items)) {
    return null;
  }
  const sourceParent = findExplorerParent(roots, items[0].id);
  if (!sourceParent) {
    return null;
  }
  if (items.some((item) => findExplorerParent(roots, item.id)?.id !== sourceParent.id)) {
    return null;
  }
  const result = pasteExplorerClipboard(roots, targetId, {
    mode: "cut",
    items: cloneExplorerForest(items),
    sourceParentId: sourceParent.id
  });
  if (!result) {
    return null;
  }
  return { tree: result.tree, moved: result.pasted, undo: result.undo };
}
function pasteExplorerClipboard(roots, locationId, clipboard) {
  if (!canPasteIntoExplorerLocation(roots, locationId, clipboard)) {
    return null;
  }
  if (clipboard.mode === "copy") {
    let tree2 = roots;
    const pasted2 = [];
    for (const item of clipboard.items) {
      const clone = cloneExplorerItemWithNewIds(tree2, item);
      tree2 = appendExplorerChild(tree2, locationId, clone);
      pasted2.push(clone);
    }
    return {
      tree: tree2,
      pasted: pasted2,
      undo: {
        type: "paste-copy",
        itemIds: pasted2.map((item) => item.id),
        parentId: locationId
      }
    };
  }
  const firstParent = findExplorerParent(roots, clipboard.items[0]?.id ?? "");
  if (!firstParent || firstParent.id === locationId) {
    return null;
  }
  let tree = roots;
  const pasted = [];
  for (const item of clipboard.items) {
    const parent = findExplorerParent(tree, item.id);
    if (!parent || parent.id !== firstParent.id) {
      return null;
    }
    const { tree: without, removed } = removeExplorerItem(tree, item.id);
    if (!removed) {
      return null;
    }
    tree = appendExplorerChild(without, locationId, removed);
    pasted.push(removed);
  }
  return {
    tree,
    pasted,
    undo: {
      type: "paste-cut",
      itemIds: pasted.map((item) => item.id),
      fromParentId: firstParent.id,
      toParentId: locationId
    }
  };
}
function deleteExplorerItems(roots, itemIds) {
  let tree = roots;
  const undos = [];
  for (const id of itemIds) {
    const result = deleteExplorerItem(tree, id);
    if (!result) {
      continue;
    }
    tree = result.tree;
    undos.push(result.undo);
  }
  if (undos.length === 0) {
    return null;
  }
  return { tree, undo: { type: "delete-many", undos } };
}
function undoExplorerDelete(roots, entry) {
  if (entry.type === "to-trash") {
    const trash = findExplorerTrashRoot(roots);
    if (!trash) {
      return null;
    }
    const { tree: without, removed } = removeExplorerItem(roots, entry.item.id);
    if (!removed) {
      return null;
    }
    if (!findExplorerItem(without, entry.parentId)) {
      return null;
    }
    return appendExplorerChild(without, entry.parentId, removed);
  }
  if (!findExplorerItem(roots, entry.parentId)) {
    return null;
  }
  return appendExplorerChild(roots, entry.parentId, entry.item);
}
function undoExplorerPaste(roots, entry) {
  if (entry.type === "paste-copy") {
    let tree2 = roots;
    for (const id of [...entry.itemIds].reverse()) {
      const { tree: next, removed } = removeExplorerItem(tree2, id);
      if (!removed) {
        return null;
      }
      tree2 = next;
    }
    return tree2;
  }
  let tree = roots;
  for (const id of [...entry.itemIds].reverse()) {
    const { tree: without, removed } = removeExplorerItem(tree, id);
    if (!removed || !findExplorerItem(without, entry.fromParentId)) {
      return null;
    }
    tree = appendExplorerChild(without, entry.fromParentId, removed);
  }
  return tree;
}
function undoExplorerAction(roots, entry) {
  if (entry.type === "to-trash" || entry.type === "permanent") {
    return undoExplorerDelete(roots, entry);
  }
  if (entry.type === "delete-many") {
    let tree = roots;
    for (const undo of [...entry.undos].reverse()) {
      const next = undoExplorerDelete(tree, undo);
      if (!next) {
        return null;
      }
      tree = next;
    }
    return tree;
  }
  return undoExplorerPaste(roots, entry);
}

// src/admin/bricks/FileExplorerWindow/SiteFileExplorer.tsx
import { jsx as jsx44, jsxs as jsxs22 } from "react/jsx-runtime";
function SiteFileExplorer({
  tree,
  initialLocationId,
  onClose,
  onMinimize,
  onMaximize,
  maximizeAction = "Maximize",
  title,
  titleIcon = "site",
  resizable = true,
  ...rest
}) {
  const rootId = initialLocationId ?? tree[0]?.id ?? "";
  const [forest, setForest] = useState7(() => cloneExplorerForest(tree));
  const [view, setView] = useState7("large-icons");
  const [locationId, setLocationId] = useState7(rootId);
  const [selectedIds, setSelectedIds] = useState7([]);
  const [selectionAnchorId, setSelectionAnchorId] = useState7(null);
  const [statusBarVisible, setStatusBarVisible] = useState7(true);
  const [clipboard, setClipboard] = useState7(null);
  const [undoEntry, setUndoEntry] = useState7(null);
  const [propertiesItem, setPropertiesItem] = useState7(null);
  const location = useMemo3(() => findExplorerItem(forest, locationId), [forest, locationId]);
  const items = useMemo3(() => explorerContentItems(location), [location]);
  const parent = useMemo3(() => findExplorerParent(forest, locationId), [forest, locationId]);
  const selectedItems = useMemo3(
    () => selectedIds.map((id) => findExplorerItem(forest, id) ?? items.find((item) => item.id === id) ?? null).filter((item) => item !== null),
    [forest, items, selectedIds]
  );
  const primarySelected = selectedItems[selectedItems.length - 1] ?? null;
  const hiddenCount = items.filter((item) => item.hidden).length;
  const statusItem = primarySelected ?? location;
  const canDelete = selectedItems.length > 0 && selectedItems.every((item) => canDeleteExplorerItem(forest, item));
  const canCutCopy = canCutOrCopyExplorerItems(forest, selectedItems);
  const canPaste = canPasteIntoExplorerLocation(forest, locationId, clipboard);
  const canProperties = selectedItems.length === 1;
  const canSelectAll = items.length > 0;
  const clearSelection = () => {
    setSelectedIds([]);
    setSelectionAnchorId(null);
  };
  const goToLocation = (item) => {
    if (item.disabled || !isExplorerLocation(item)) {
      return;
    }
    setLocationId(item.id);
    clearSelection();
  };
  const handleSelect = (item, modifiers) => {
    const additive = modifiers.ctrlKey || modifiers.metaKey;
    if (modifiers.shiftKey && selectionAnchorId) {
      const anchorIndex = items.findIndex((entry) => entry.id === selectionAnchorId);
      const targetIndex = items.findIndex((entry) => entry.id === item.id);
      if (anchorIndex >= 0 && targetIndex >= 0) {
        const [from, to] = anchorIndex < targetIndex ? [anchorIndex, targetIndex] : [targetIndex, anchorIndex];
        const rangeIds = items.slice(from, to + 1).map((entry) => entry.id);
        if (additive) {
          setSelectedIds((prev) => Array.from(/* @__PURE__ */ new Set([...prev, ...rangeIds])));
        } else {
          setSelectedIds(rangeIds);
        }
        return;
      }
    }
    if (additive) {
      setSelectedIds(
        (prev) => prev.includes(item.id) ? prev.filter((id) => id !== item.id) : [...prev, item.id]
      );
      setSelectionAnchorId(item.id);
      return;
    }
    setSelectedIds([item.id]);
    setSelectionAnchorId(item.id);
  };
  const handleSelectAll = () => {
    if (!canSelectAll) {
      return;
    }
    setSelectedIds(items.map((item) => item.id));
    setSelectionAnchorId(items[0]?.id ?? null);
  };
  const handleCut = () => {
    if (!canCutCopy || selectedItems.length === 0) {
      return;
    }
    const sourceParent = findExplorerParent(forest, selectedItems[0].id);
    if (!sourceParent) {
      return;
    }
    if (selectedItems.some((item) => findExplorerParent(forest, item.id)?.id !== sourceParent.id)) {
      return;
    }
    setClipboard({
      mode: "cut",
      items: cloneExplorerForest(selectedItems),
      sourceParentId: sourceParent.id
    });
  };
  const handleCopy = () => {
    if (!canCutCopy || selectedItems.length === 0) {
      return;
    }
    setClipboard({
      mode: "copy",
      items: cloneExplorerForest(selectedItems),
      sourceParentId: null
    });
  };
  const handlePaste = () => {
    if (!clipboard || !locationId) {
      return;
    }
    const result = pasteExplorerClipboard(forest, locationId, clipboard);
    if (!result) {
      return;
    }
    setForest(result.tree);
    setUndoEntry(result.undo);
    setSelectedIds(result.pasted.map((item) => item.id));
    setSelectionAnchorId(result.pasted[0]?.id ?? null);
    if (clipboard.mode === "cut") {
      setClipboard(null);
    }
  };
  const handleDelete = () => {
    if (selectedIds.length === 0) {
      return;
    }
    const result = deleteExplorerItems(forest, selectedIds);
    if (!result) {
      return;
    }
    if (clipboard?.items.some((item) => selectedIds.includes(item.id))) {
      setClipboard(null);
    }
    if (propertiesItem && selectedIds.includes(propertiesItem.id)) {
      setPropertiesItem(null);
    }
    setForest(result.tree);
    setUndoEntry(result.undo);
    clearSelection();
  };
  const handleItemsDrop = (itemIds, targetId) => {
    const result = moveExplorerItems(forest, itemIds, targetId);
    if (!result) {
      return;
    }
    if (clipboard?.items.some((item) => itemIds.includes(item.id))) {
      setClipboard(null);
    }
    setForest(result.tree);
    setUndoEntry(result.undo);
    setSelectedIds(result.moved.map((item) => item.id));
    setSelectionAnchorId(result.moved[0]?.id ?? null);
  };
  const handleUndo = () => {
    if (!undoEntry) {
      return;
    }
    const next = undoExplorerAction(forest, undoEntry);
    if (!next) {
      setUndoEntry(null);
      return;
    }
    setForest(next);
    if (undoEntry.type === "to-trash" || undoEntry.type === "permanent") {
      setSelectedIds([undoEntry.item.id]);
      setSelectionAnchorId(undoEntry.item.id);
    } else if (undoEntry.type === "delete-many") {
      const ids = undoEntry.undos.map((entry) => entry.item.id);
      setSelectedIds(ids);
      setSelectionAnchorId(ids[0] ?? null);
    } else {
      setSelectedIds(undoEntry.itemIds);
      setSelectionAnchorId(undoEntry.itemIds[0] ?? null);
    }
    setUndoEntry(null);
  };
  const propertiesParentLabel = propertiesItem ? findExplorerParent(forest, propertiesItem.id)?.label ?? null : null;
  const statusCountLabel = selectedIds.length > 0 ? `${selectedIds.length} object(s) selected` : `${items.length} object(s)${hiddenCount > 0 ? ` (${hiddenCount} hidden)` : ""}`;
  return /* @__PURE__ */ jsxs22("div", { className: "site-file-explorer", children: [
    /* @__PURE__ */ jsx44(
      FileExplorerWindow,
      {
        title,
        titleIcon,
        ...rest,
        resizable,
        tree: forest,
        items,
        view,
        onViewChange: setView,
        locationId,
        selectedIds,
        cutItemIds: clipboard?.mode === "cut" ? clipboard.items.map((item) => item.id) : [],
        onTreeSelect: goToLocation,
        onSelect: handleSelect,
        onOpen: goToLocation,
        onItemsDrop: handleItemsDrop,
        onLevelUp: () => {
          if (!parent) {
            return;
          }
          setLocationId(parent.id);
          clearSelection();
        },
        levelUpDisabled: !parent,
        onClose,
        onCut: canCutCopy ? handleCut : void 0,
        onCopy: canCutCopy ? handleCopy : void 0,
        onPaste: canPaste ? handlePaste : void 0,
        onDelete: canDelete ? handleDelete : void 0,
        onUndo: undoEntry ? handleUndo : void 0,
        onSelectAll: canSelectAll ? handleSelectAll : void 0,
        onProperties: canProperties ? () => {
          if (primarySelected) {
            setPropertiesItem(primarySelected);
          }
        } : void 0,
        statusBarVisible,
        onStatusBarToggle: () => setStatusBarVisible((value) => !value),
        titleBarControls: /* @__PURE__ */ jsxs22(TitleBarControls, { children: [
          /* @__PURE__ */ jsx44(TitleBarControl, { action: "Minimize", onClick: onMinimize }),
          resizable ? /* @__PURE__ */ jsx44(TitleBarControl, { action: maximizeAction, onClick: onMaximize }) : null,
          /* @__PURE__ */ jsx44(TitleBarControl, { action: "Close", onClick: onClose })
        ] }),
        statusBar: statusBarVisible ? /* @__PURE__ */ jsxs22(StatusBar, { children: [
          /* @__PURE__ */ jsx44(StatusBarField, { children: statusCountLabel }),
          /* @__PURE__ */ jsx44(StatusBarField, { className: "description", children: statusItem?.typeLabel ?? "" }),
          /* @__PURE__ */ jsx44(StatusBarField, {})
        ] }) : void 0
      }
    ),
    propertiesItem ? /* @__PURE__ */ jsx44(DesktopModal, { children: /* @__PURE__ */ jsx44(
      ExplorerPropertiesDialog,
      {
        item: propertiesItem,
        parentLabel: propertiesParentLabel,
        onClose: () => setPropertiesItem(null)
      }
    ) }) : null
  ] });
}

// src/admin/bricks/FileExplorerWindow/FileExplorerWindow.data.ts
var EXPLORER_FIXTURE_SITE = {
  id: "site-acme",
  name: "Acme Website",
  /** Default site glyph until favicon support exists. */
  titleIcon: "site"
};
var EXPLORER_FIXTURE_TREE = [
  {
    id: "site-acme",
    label: EXPLORER_FIXTURE_SITE.name,
    kind: "site",
    role: "site",
    typeLabel: "Website",
    children: [
      {
        id: "nav-home",
        label: "Home",
        kind: "file-document",
        role: "document",
        typeLabel: "HTML Document",
        sizeBytes: 4096,
        modifiedAt: "7/12/26 9:00 AM"
      },
      {
        id: "nav-about",
        label: "About",
        kind: "folder",
        role: "folder",
        typeLabel: "Folder",
        modifiedAt: "7/10/26 2:15 PM",
        children: [
          {
            id: "nav-about-team",
            label: "Team",
            kind: "file-document",
            role: "document",
            typeLabel: "HTML Document",
            sizeBytes: 3072,
            modifiedAt: "7/10/26 2:15 PM"
          },
          {
            id: "nav-about-history",
            label: "History",
            kind: "file-document",
            role: "document",
            typeLabel: "HTML Document",
            sizeBytes: 2560,
            modifiedAt: "6/01/26 11:30 AM"
          }
        ]
      },
      {
        id: "nav-blog",
        label: "Blog",
        kind: "folder",
        role: "folder",
        typeLabel: "Folder",
        modifiedAt: "7/20/26 4:00 PM",
        children: [
          {
            id: "nav-blog-index",
            label: "Index",
            kind: "file-document",
            role: "document",
            typeLabel: "HTML Document",
            sizeBytes: 5120,
            modifiedAt: "7/20/26 4:00 PM"
          }
        ]
      },
      {
        id: "nav-contact",
        label: "Contact",
        kind: "file-document",
        role: "document",
        typeLabel: "HTML Document",
        sizeBytes: 2048,
        modifiedAt: "5/18/26 8:45 AM"
      }
    ]
  },
  {
    id: "media-library",
    label: "Media library",
    kind: "folder-gallery",
    role: "media-library",
    typeLabel: "Media Library",
    children: [
      {
        id: "media-hero",
        label: "hero.jpg",
        kind: "file-image",
        role: "media-asset",
        typeLabel: "JPEG Image",
        sizeBytes: 245760,
        modifiedAt: "7/01/26 10:12 AM"
      },
      {
        id: "media-logo",
        label: "logo.png",
        kind: "file-image",
        role: "media-asset",
        typeLabel: "PNG Image",
        sizeBytes: 18432,
        modifiedAt: "6/15/26 3:20 PM"
      },
      {
        id: "media-gallery",
        label: "gallery",
        kind: "folder",
        role: "folder",
        typeLabel: "Folder",
        modifiedAt: "7/08/26 1:00 PM",
        children: [
          {
            id: "media-gallery-1",
            label: "photo-01.jpg",
            kind: "file-image",
            role: "media-asset",
            typeLabel: "JPEG Image",
            sizeBytes: 102400,
            modifiedAt: "7/08/26 1:00 PM"
          }
        ]
      }
    ]
  },
  {
    id: "trash",
    label: "Recycle Bin",
    kind: "trash",
    role: "trash",
    typeLabel: "Recycle Bin",
    expandable: false,
    children: [
      {
        id: "trash-old-landing",
        label: "Old landing",
        kind: "file-document",
        role: "document",
        typeLabel: "HTML Document",
        sizeBytes: 8192,
        modifiedAt: "4/02/26 5:10 PM"
      },
      {
        id: "trash-drafts",
        label: "Drafts",
        kind: "folder",
        role: "folder",
        typeLabel: "Folder",
        modifiedAt: "3/11/26 9:00 AM"
      }
    ]
  },
  {
    id: "settings",
    label: "Settings",
    kind: "settings",
    role: "settings",
    typeLabel: "Settings",
    expandable: false,
    disabled: true
  }
];
var EXPLORER_FIXTURE_ITEMS = EXPLORER_FIXTURE_TREE.find((n) => n.id === "site-acme")?.children ?? [];
function buildEmptySiteExplorerTree(site) {
  const prefix = `site-${site.id}`;
  return [
    {
      id: prefix,
      label: site.name,
      kind: "site",
      role: "site",
      typeLabel: "Website",
      children: []
    },
    {
      id: `${prefix}-media`,
      label: "Media library",
      kind: "folder-gallery",
      role: "media-library",
      typeLabel: "Media Library",
      children: []
    },
    {
      id: `${prefix}-trash`,
      label: "Recycle Bin",
      kind: "trash",
      role: "trash",
      typeLabel: "Recycle Bin",
      expandable: false,
      children: []
    },
    {
      id: `${prefix}-settings`,
      label: "Settings",
      kind: "settings",
      role: "settings",
      typeLabel: "Settings",
      expandable: false,
      disabled: true
    }
  ];
}
function buildDemoSiteExplorerTree(site) {
  const prefix = `site-${site.id}`;
  const remap = (nodes, path) => nodes.map((node) => ({
    ...node,
    id: `${path}/${node.id}`,
    children: node.children ? remap(node.children, `${path}/${node.id}`) : void 0
  }));
  const [siteRoot, media, trash, settings] = EXPLORER_FIXTURE_TREE;
  return [
    {
      ...siteRoot,
      id: prefix,
      label: site.name,
      children: siteRoot.children ? remap(siteRoot.children, prefix) : []
    },
    {
      ...media,
      id: `${prefix}-media`,
      children: media.children ? remap(media.children, `${prefix}-media`) : []
    },
    {
      ...trash,
      id: `${prefix}-trash`,
      children: trash.children ? remap(trash.children, `${prefix}-trash`) : []
    },
    {
      ...settings,
      id: `${prefix}-settings`
    }
  ];
}

// src/admin/api/client.ts
var DEFAULT_BASE = "/admin/api";
var SESSION_EXPIRED_MESSAGE = "Your session has expired. Please sign in again.";
function normalizeBase(baseUrl) {
  return baseUrl.replace(/\/+$/, "");
}
function unauthorizedResult() {
  return {
    ok: false,
    status: 401,
    error: {
      code: "unauthorized",
      message: SESSION_EXPIRED_MESSAGE
    }
  };
}
function pathLooksLikeLogin(urlOrPath) {
  try {
    const path = new URL(urlOrPath, "http://localhost").pathname;
    return path === "/login" || path.endsWith("/login");
  } catch {
    return /\/login(?:\?|$)/.test(urlOrPath);
  }
}
function isAuthFailureResponse(response) {
  if (response.status === 401) {
    return true;
  }
  if (response.type === "opaqueredirect") {
    return true;
  }
  if ([301, 302, 303, 307, 308].includes(response.status)) {
    const location = response.headers.get("Location");
    return location != null && pathLooksLikeLogin(location);
  }
  if (response.url && pathLooksLikeLogin(response.url)) {
    return true;
  }
  return false;
}
async function parseResult(response) {
  if (isAuthFailureResponse(response)) {
    return unauthorizedResult();
  }
  if (response.status === 204) {
    return { ok: true, status: 204, data: void 0 };
  }
  let payload;
  try {
    payload = await response.json();
  } catch {
    if (response.status === 401 || response.url && pathLooksLikeLogin(response.url)) {
      return unauthorizedResult();
    }
    return {
      ok: false,
      status: response.status,
      error: {
        code: "invalid_json",
        message: "Server returned a non-JSON response."
      }
    };
  }
  if (response.ok) {
    const data = payload && typeof payload === "object" && "data" in payload ? payload.data : payload;
    return { ok: true, status: response.status, data };
  }
  const error = payload && typeof payload === "object" && "error" in payload && payload.error && typeof payload.error === "object" ? payload.error : {
    code: "http_error",
    message: `Request failed (${response.status}).`
  };
  return {
    ok: false,
    status: response.status,
    error: {
      code: typeof error.code === "string" ? error.code : "http_error",
      message: typeof error.message === "string" ? error.message : `Request failed (${response.status}).`,
      fields: error.fields && typeof error.fields === "object" ? error.fields : void 0
    }
  };
}
function createAdminApiClient(options = {}) {
  const baseUrl = normalizeBase(options.baseUrl ?? DEFAULT_BASE);
  const fetchImpl = options.fetch ?? globalThis.fetch.bind(globalThis);
  const csrfToken = options.csrfToken;
  async function request(path, init = {}) {
    const headers = new Headers(init.headers);
    if (!headers.has("Accept")) {
      headers.set("Accept", "application/json");
    }
    if (init.body != null && !headers.has("Content-Type")) {
      headers.set("Content-Type", "application/json");
    }
    const method = (init.method ?? "GET").toUpperCase();
    if (csrfToken && method !== "GET" && method !== "HEAD") {
      headers.set("X-CSRF-TOKEN", csrfToken);
    }
    const response = await fetchImpl(`${baseUrl}${path}`, {
      ...init,
      headers,
      credentials: "same-origin",
      // Detect form_login bounce to /login instead of parsing HTML as JSON.
      redirect: "manual"
    });
    return parseResult(response);
  }
  return {
    listSites: () => request("/sites"),
    getSite: (id) => request(`/sites/${id}`),
    createSite: (body) => request("/sites", {
      method: "POST",
      body: JSON.stringify(body)
    }),
    updateSite: (id, body) => request(`/sites/${id}`, {
      method: "PATCH",
      body: JSON.stringify(body)
    }),
    deleteSite: (id) => request(`/sites/${id}`, {
      method: "DELETE"
    }),
    listHosts: () => request("/hosts"),
    getHost: (id) => request(`/hosts/${id}`),
    createHost: (body) => request("/hosts", {
      method: "POST",
      body: JSON.stringify(body)
    }),
    updateHost: (id, body) => request(`/hosts/${id}`, {
      method: "PATCH",
      body: JSON.stringify(body)
    }),
    deleteHost: (id) => request(`/hosts/${id}`, {
      method: "DELETE"
    }),
    unassignHost: (id) => request(`/hosts/${id}/unassign`, {
      method: "POST"
    }),
    verifyHost: (id) => request(`/hosts/${id}/verify`, {
      method: "POST"
    }),
    assignHost: (id, body) => request(`/hosts/${id}/assign`, {
      method: "POST",
      body: JSON.stringify(body)
    })
  };
}
function isUnauthorizedResult(result) {
  return !result.ok && (result.status === 401 || result.error.code === "unauthorized");
}

// src/admin/components/FlashList/FlashList.tsx
import { jsx as jsx45 } from "react/jsx-runtime";
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
    ([tone, messages]) => messages.map((message, index) => /* @__PURE__ */ jsx45(Alert, { tone: toneForFlash(tone), className: "mb-4", children: message }, `${tone}-${index}`))
  );
}

// src/admin/components/DataTable/DataTable.tsx
import { jsx as jsx46, jsxs as jsxs23 } from "react/jsx-runtime";
function DataTable({
  columns,
  rows,
  rowKey,
  emptyMessage = "No records found.",
  loading = false,
  className
}) {
  if (loading) {
    return /* @__PURE__ */ jsx46("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: "Loading\u2026" });
  }
  if (rows.length === 0) {
    return /* @__PURE__ */ jsx46("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-dashed border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: emptyMessage });
  }
  return /* @__PURE__ */ jsx46(
    "div",
    {
      className: cn(
        "wh-ui overflow-x-auto rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs23("table", { className: "w-full border-collapse text-left text-sm", children: [
        /* @__PURE__ */ jsx46("thead", { className: "bg-[var(--wh-color-canvas)] text-[var(--wh-color-muted)]", children: /* @__PURE__ */ jsx46("tr", { children: columns.map((col) => /* @__PURE__ */ jsx46("th", { className: cn("px-4 py-3 font-semibold", col.className), children: col.header }, col.key)) }) }),
        /* @__PURE__ */ jsx46("tbody", { children: rows.map((row) => /* @__PURE__ */ jsx46(
          "tr",
          {
            className: "border-t border-[var(--wh-color-line)] hover:bg-[color-mix(in_srgb,var(--wh-color-accent)_6%,white)]",
            children: columns.map((col) => /* @__PURE__ */ jsx46("td", { className: cn("px-4 py-3", col.className), children: col.render(row) }, col.key))
          },
          rowKey(row)
        )) })
      ] })
    }
  );
}

// src/admin/components/Pagination/Pagination.tsx
import { jsx as jsx47, jsxs as jsxs24 } from "react/jsx-runtime";
function Pagination({ page, pageCount, onPageChange, className }) {
  if (pageCount <= 1) {
    return null;
  }
  return /* @__PURE__ */ jsxs24(
    "nav",
    {
      className: cn("wh-ui mt-4 flex items-center justify-between gap-3", className),
      "aria-label": "Pagination",
      children: [
        /* @__PURE__ */ jsx47(
          Button,
          {
            variant: "secondary",
            size: "sm",
            disabled: page <= 1,
            onClick: () => onPageChange(page - 1),
            children: "Previous"
          }
        ),
        /* @__PURE__ */ jsxs24("span", { className: "text-sm text-[var(--wh-color-muted)]", children: [
          "Page ",
          page,
          " of ",
          pageCount
        ] }),
        /* @__PURE__ */ jsx47(
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
import { useEffect as useEffect6 } from "react";
import { jsx as jsx48, jsxs as jsxs25 } from "react/jsx-runtime";
function Modal({ open, title, children, onClose, footer, className }) {
  useEffect6(() => {
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
  return /* @__PURE__ */ jsxs25("div", { className: "wh-ui fixed inset-0 z-50 flex items-center justify-center p-4", children: [
    /* @__PURE__ */ jsx48(
      "button",
      {
        type: "button",
        className: "absolute inset-0 bg-[var(--wh-color-ink)]/50",
        "aria-label": "Close dialog",
        onClick: onClose
      }
    ),
    /* @__PURE__ */ jsxs25(
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
          /* @__PURE__ */ jsxs25("div", { className: "flex items-center justify-between border-b border-[var(--wh-color-line)] px-4 py-3", children: [
            /* @__PURE__ */ jsx48("h2", { id: "wh-modal-title", className: "font-[family-name:var(--wh-font-display)] text-lg", children: title }),
            /* @__PURE__ */ jsx48(Button, { variant: "ghost", size: "sm", onClick: onClose, "aria-label": "Close", children: "\xD7" })
          ] }),
          /* @__PURE__ */ jsx48("div", { className: "px-4 py-4", children }),
          footer ? /* @__PURE__ */ jsx48("div", { className: "flex justify-end gap-2 border-t border-[var(--wh-color-line)] px-4 py-3", children: footer }) : null
        ]
      }
    )
  ] });
}

// src/admin/components/PageHeader/PageHeader.tsx
import { jsx as jsx49, jsxs as jsxs26 } from "react/jsx-runtime";
function PageHeader({ title, description, actions, className }) {
  return /* @__PURE__ */ jsxs26(
    "header",
    {
      className: cn(
        "wh-ui mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-[var(--wh-color-line)] pb-4",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs26("div", { children: [
          /* @__PURE__ */ jsx49("h1", { className: "font-[family-name:var(--wh-font-display)] text-3xl tracking-tight text-[var(--wh-color-ink)]", children: title }),
          description ? /* @__PURE__ */ jsx49("p", { className: "mt-1 max-w-2xl text-[var(--wh-color-muted)]", children: description }) : null
        ] }),
        actions ? /* @__PURE__ */ jsx49("div", { className: "flex flex-wrap gap-2", children: actions }) : null
      ]
    }
  );
}

// src/admin/components/Sidebar/Sidebar.tsx
import { jsx as jsx50, jsxs as jsxs27 } from "react/jsx-runtime";
function Sidebar({ brand = "WebHemi", items, className }) {
  return /* @__PURE__ */ jsxs27(
    "aside",
    {
      className: cn(
        "wh-ui flex min-h-full w-60 flex-col border-r border-[var(--wh-color-line)] bg-[var(--wh-color-ink)] text-white",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs27("div", { className: "border-b border-white/10 px-5 py-5", children: [
          /* @__PURE__ */ jsx50("p", { className: "font-[family-name:var(--wh-font-display)] text-2xl tracking-tight", children: brand }),
          /* @__PURE__ */ jsx50("p", { className: "text-xs uppercase tracking-[0.2em] text-white/60", children: "Admin" })
        ] }),
        /* @__PURE__ */ jsx50("nav", { className: "flex flex-1 flex-col gap-1 p-3", "aria-label": "Admin", children: items.map((item) => /* @__PURE__ */ jsxs27(
          "a",
          {
            href: item.href,
            className: cn(
              "flex items-center gap-3 rounded-[var(--wh-radius-sm)] px-3 py-2 text-sm transition",
              item.active ? "bg-[var(--wh-color-accent)] text-white" : "text-white/80 hover:bg-white/10 hover:text-white"
            ),
            "aria-current": item.active ? "page" : void 0,
            children: [
              item.icon ? /* @__PURE__ */ jsx50(Icon, { name: item.icon }) : null,
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
import { jsx as jsx51, jsxs as jsxs28 } from "react/jsx-runtime";
function TopBar({ title, userLabel, actions, className }) {
  return /* @__PURE__ */ jsxs28(
    "div",
    {
      className: cn(
        "wh-ui flex items-center justify-between gap-4 border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] px-6 py-3",
        className
      ),
      children: [
        /* @__PURE__ */ jsx51("p", { className: "text-sm font-medium text-[var(--wh-color-muted)]", children: title ?? "Control panel" }),
        /* @__PURE__ */ jsxs28("div", { className: "flex items-center gap-3", children: [
          actions,
          userLabel ? /* @__PURE__ */ jsx51("span", { className: "rounded-[var(--wh-radius-sm)] bg-[var(--wh-color-canvas)] px-3 py-1 text-sm", children: userLabel }) : null
        ] })
      ]
    }
  );
}

// src/admin/components/AdminLayout/AdminLayout.tsx
import { jsx as jsx52, jsxs as jsxs29 } from "react/jsx-runtime";
function AdminLayout({
  brand,
  navItems,
  userLabel,
  topBarTitle,
  topBarActions,
  children,
  className
}) {
  return /* @__PURE__ */ jsxs29("div", { className: cn("wh-ui flex min-h-screen bg-[var(--wh-color-canvas)]", className), children: [
    /* @__PURE__ */ jsx52(Sidebar, { brand, items: navItems }),
    /* @__PURE__ */ jsxs29("div", { className: "flex min-w-0 flex-1 flex-col", children: [
      /* @__PURE__ */ jsx52(TopBar, { title: topBarTitle, userLabel, actions: topBarActions }),
      /* @__PURE__ */ jsx52("main", { className: "flex-1 p-6", children })
    ] })
  ] });
}

// src/admin/components/LoginForm/LoginForm.tsx
import { jsx as jsx53, jsxs as jsxs30 } from "react/jsx-runtime";
function LoginForm({
  action = "/login",
  method = "post",
  csrfToken,
  csrfFieldName = "_csrf_token",
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
  return /* @__PURE__ */ jsxs30(
    "form",
    {
      action,
      method,
      onSubmit: handleSubmit,
      noValidate: true,
      className: cn(className),
      children: [
        csrfToken ? /* @__PURE__ */ jsx53("input", { type: "hidden", name: csrfFieldName, value: csrfToken }) : null,
        /* @__PURE__ */ jsxs30(
          DialogWindow,
          {
            title: "Sign in \u2014 WebHemi CMS Admin",
            titleBarControls: null,
            banner: /* @__PURE__ */ jsx53("img", { alt: "", className: "dialog-banner", src: bannerSrc }),
            actions: /* @__PURE__ */ jsx53(FieldRow, { className: "justify-end", children: /* @__PURE__ */ jsx53(Button2, { type: "submit", isDefault: true, loading, children: "OK" }) }),
            children: [
              /* @__PURE__ */ jsx53(FieldRow, { children: /* @__PURE__ */ jsx53(
                TextBox,
                {
                  id: "email",
                  name: "email",
                  type: "email",
                  label: "Email:",
                  accessKey: "e",
                  autoComplete: "username",
                  defaultValue: emailDefault,
                  className: "w-window-xs"
                }
              ) }),
              /* @__PURE__ */ jsx53(FieldRow, { children: /* @__PURE__ */ jsx53(
                TextBox,
                {
                  id: "password",
                  name: "password",
                  type: "password",
                  label: "Password:",
                  accessKey: "p",
                  autoComplete: "current-password",
                  className: "w-window-xs"
                }
              ) })
            ]
          }
        )
      ]
    }
  );
}

// src/admin/components/ControlPanel/ControlPanel.tsx
import { useState as useState8 } from "react";
import { jsx as jsx54, jsxs as jsxs31 } from "react/jsx-runtime";
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
  onOpenSites,
  onOpenHosts,
  onMinimize,
  onMaximize,
  onActivate,
  inactive = false,
  maximized = false,
  resizable = true,
  className,
  style,
  width = 600,
  paneHeight = 300
}) {
  const [selected, setSelected] = useState8(null);
  return /* @__PURE__ */ jsx54(
    IconPanelWindow,
    {
      className,
      style,
      width,
      inactive,
      resizable,
      paneHeight,
      title: "Control Panel",
      titleIcon: "control-panel",
      titleBarControls: /* @__PURE__ */ jsxs31(TitleBarControls, { children: [
        /* @__PURE__ */ jsx54(TitleBarControl, { action: "Minimize", onClick: onMinimize }),
        resizable ? /* @__PURE__ */ jsx54(
          TitleBarControl,
          {
            action: maximized ? "Restore" : "Maximize",
            onClick: onMaximize
          }
        ) : null,
        /* @__PURE__ */ jsx54(TitleBarControl, { action: "Close", onClick: onClose })
      ] }),
      onMouseDown: onActivate,
      infoUnselected: !selected,
      info: selected ? /* @__PURE__ */ jsx54(
        IconPanelSelectionInfo,
        {
          kind: selected.kind,
          label: selected.label,
          description: selected.description
        }
      ) : null,
      statusBar: /* @__PURE__ */ jsxs31(StatusBar, { children: [
        /* @__PURE__ */ jsxs31(StatusBarField, { children: [
          ICONS.length,
          " items"
        ] }),
        /* @__PURE__ */ jsx54(StatusBarField, { className: "description", children: selected?.description ?? "" }),
        /* @__PURE__ */ jsx54(StatusBarField, {})
      ] }),
      children: ICONS.map((icon) => {
        const onOpen = icon.kind === "sites" ? onOpenSites : icon.kind === "hosts" ? onOpenHosts : void 0;
        return /* @__PURE__ */ jsx54(
          SystemIcon,
          {
            kind: icon.kind,
            label: icon.label,
            labelTone: "dark",
            onActivate: () => setSelected(icon),
            onOpen
          },
          icon.kind
        );
      })
    }
  );
}

// src/admin/components/SitesWindow/SitesWindow.tsx
import { useCallback as useCallback3, useEffect as useEffect8, useLayoutEffect as useLayoutEffect3, useRef as useRef6, useState as useState10 } from "react";

// src/admin/components/SitesWindow/SiteFormDialog.tsx
import { useEffect as useEffect7, useId as useId2, useMemo as useMemo4, useState as useState9 } from "react";
import { Fragment as Fragment8, jsx as jsx55, jsxs as jsxs32 } from "react/jsx-runtime";
function SiteFormDialog({
  mode,
  initial,
  hosts = [],
  fieldErrors,
  saving = false,
  unassigning = false,
  assigning = false,
  onSave,
  onError,
  onClose,
  onAddHost,
  onAssignHost,
  onUnassignHost,
  className
}) {
  const nameId = useId2();
  const slugId = useId2();
  const enabledId = useId2();
  const assignSelectId = useId2();
  const [tab, setTab] = useState9("general");
  const [name, setName] = useState9(initial?.name ?? "");
  const [slug, setSlug] = useState9(initial?.slug ?? "");
  const [enabled, setEnabled] = useState9(initial?.enabled ?? true);
  const [selectedHostId, setSelectedHostId] = useState9(null);
  const [assignHostId, setAssignHostId] = useState9(null);
  const [localErrors, setLocalErrors] = useState9(
    {}
  );
  const assignedHosts = useMemo4(() => {
    if (initial?.siteId == null) {
      return [];
    }
    return hosts.filter((host) => host.siteId === initial.siteId);
  }, [hosts, initial?.siteId]);
  const assignableHosts = useMemo4(
    () => hosts.filter(
      (host) => host.status === "verified" && (host.siteId == null || host.siteId === void 0)
    ),
    [hosts]
  );
  useEffect7(() => {
    setLocalErrors({});
  }, [fieldErrors]);
  useEffect7(() => {
    if (selectedHostId != null && !assignedHosts.some((host) => host.id === selectedHostId)) {
      setSelectedHostId(null);
    }
  }, [assignedHosts, selectedHostId]);
  useEffect7(() => {
    if (assignHostId != null && !assignableHosts.some((host) => host.id === assignHostId)) {
      setAssignHostId(null);
    }
  }, [assignableHosts, assignHostId]);
  const errors = { ...localErrors, ...fieldErrors };
  const title = mode === "new" ? "New Site" : `${initial?.title ?? initial?.name ?? "Site"} Properties`;
  const busy = saving || unassigning || assigning;
  const canRemove = Boolean(onUnassignHost) && selectedHostId != null && initial?.siteId != null && !busy;
  const canAssign = Boolean(onAssignHost) && assignHostId != null && initial?.siteId != null && !busy;
  const handleSubmit = (event) => {
    event.preventDefault();
    if (busy) {
      return;
    }
    const nextName = name.trim();
    const nextSlug = slug.trim().toLowerCase();
    const nextLocal = {};
    if (!nextName) {
      nextLocal.name = "Name is required.";
    }
    if (!nextSlug) {
      nextLocal.slug = "Slug is required.";
    } else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(nextSlug)) {
      nextLocal.slug = "Use lowercase letters, digits, and hyphens.";
    }
    setLocalErrors(nextLocal);
    if (Object.keys(nextLocal).length > 0) {
      setTab("general");
      onError?.(Object.values(nextLocal).join("\n"));
      return;
    }
    onSave({
      mode,
      siteId: initial?.siteId,
      name: nextName,
      slug: nextSlug,
      enabled
    });
  };
  const handleRemove = () => {
    if (!canRemove || selectedHostId == null) {
      return;
    }
    onUnassignHost?.(selectedHostId);
  };
  const handleAssign = () => {
    if (!canAssign || assignHostId == null) {
      return;
    }
    onAssignHost?.(assignHostId);
  };
  return /* @__PURE__ */ jsx55(
    PaneWindowShell,
    {
      className: cn("site-form-dialog", className),
      width: 480,
      title,
      titleIcon: "sites",
      titleBarControls: /* @__PURE__ */ jsx55(TitleBarControls, { children: /* @__PURE__ */ jsx55(TitleBarControl, { action: "Close", onClick: onClose }) }),
      children: /* @__PURE__ */ jsxs32("form", { className: "site-form-dialog-form", onSubmit: handleSubmit, noValidate: true, children: [
        /* @__PURE__ */ jsxs32(TabList, { children: [
          /* @__PURE__ */ jsx55(
            Tab,
            {
              selected: tab === "general",
              href: "#site-form-general",
              onClick: (event) => {
                event.preventDefault();
                setTab("general");
              },
              children: "General"
            }
          ),
          /* @__PURE__ */ jsx55(
            Tab,
            {
              selected: tab === "hosts",
              href: "#site-form-hosts",
              onClick: (event) => {
                event.preventDefault();
                setTab("hosts");
              },
              children: "Hosts"
            }
          )
        ] }),
        /* @__PURE__ */ jsx55(TabPanel, { children: /* @__PURE__ */ jsx55(WindowBody, { children: tab === "general" ? /* @__PURE__ */ jsxs32(Fragment8, { children: [
          /* @__PURE__ */ jsx55(FieldRow, { children: /* @__PURE__ */ jsx55(
            TextBox,
            {
              id: nameId,
              label: "Name:",
              accessKey: "n",
              value: name,
              disabled: busy,
              "aria-invalid": Boolean(errors.name) || void 0,
              onChange: (event) => setName(event.target.value)
            }
          ) }),
          /* @__PURE__ */ jsx55(FieldRow, { children: /* @__PURE__ */ jsx55(
            TextBox,
            {
              id: slugId,
              label: "Slug:",
              accessKey: "s",
              value: slug,
              disabled: busy,
              "aria-invalid": Boolean(errors.slug) || void 0,
              onChange: (event) => setSlug(event.target.value)
            }
          ) }),
          /* @__PURE__ */ jsx55(FieldRow, { children: /* @__PURE__ */ jsx55(
            Checkbox,
            {
              id: enabledId,
              label: "Enabled",
              accessKey: "e",
              checked: enabled,
              disabled: busy,
              onChange: (event) => setEnabled(event.target.checked)
            }
          ) })
        ] }) : /* @__PURE__ */ jsxs32(Fragment8, { children: [
          /* @__PURE__ */ jsx55("p", { style: { marginTop: 0, marginBottom: 8 }, children: initial?.siteId == null ? "Save the site first, then assign verified hosts here or from Hosts." : "Assigned hosts below. Assign only verified, unassigned hosts; Remove unassigns without deleting." }),
          /* @__PURE__ */ jsx55(
            SunkenPanel,
            {
              scrollable: true,
              tone: "white",
              className: "site-form-host-list",
              children: assignedHosts.length === 0 ? /* @__PURE__ */ jsx55("p", { style: { margin: 8 }, children: initial?.siteId == null ? "No hosts until this site is saved." : "No hosts assigned." }) : /* @__PURE__ */ jsxs32(Table, { "aria-label": "Assigned hosts", children: [
                /* @__PURE__ */ jsx55("thead", { children: /* @__PURE__ */ jsxs32("tr", { children: [
                  /* @__PURE__ */ jsx55("th", { children: "Name" }),
                  /* @__PURE__ */ jsx55("th", { children: "Verification" })
                ] }) }),
                /* @__PURE__ */ jsx55("tbody", { children: assignedHosts.map((row) => /* @__PURE__ */ jsxs32(
                  TableRow,
                  {
                    highlighted: selectedHostId === row.id,
                    onClick: () => setSelectedHostId(
                      (current) => current === row.id ? null : row.id
                    ),
                    children: [
                      /* @__PURE__ */ jsx55("td", { children: row.host }),
                      /* @__PURE__ */ jsx55("td", { children: row.status })
                    ]
                  },
                  row.id
                )) })
              ] })
            }
          ),
          initial?.siteId != null ? /* @__PURE__ */ jsxs32(FieldRow, { style: { marginTop: 8 }, children: [
            /* @__PURE__ */ jsxs32(
              Select2,
              {
                id: assignSelectId,
                label: "Assign:",
                accessKey: "i",
                value: assignHostId != null ? String(assignHostId) : "",
                disabled: busy || !onAssignHost || assignableHosts.length === 0,
                title: assignableHosts.length === 0 ? "No verified, unassigned hosts available" : "Verified hosts not bound to a site",
                onChange: (event) => {
                  const value = event.target.value;
                  setAssignHostId(value === "" ? null : Number(value));
                },
                children: [
                  /* @__PURE__ */ jsx55("option", { value: "", children: assignableHosts.length === 0 ? "None available" : "Select a host\u2026" }),
                  assignableHosts.map((host) => /* @__PURE__ */ jsx55("option", { value: host.id, children: host.host }, host.id))
                ]
              }
            ),
            /* @__PURE__ */ jsx55(
              Button2,
              {
                type: "button",
                accessKey: "g",
                disabled: !canAssign,
                title: "Assign selected verified host to this site",
                onClick: handleAssign,
                children: "Assign"
              }
            )
          ] }) : null,
          /* @__PURE__ */ jsxs32(FieldRow, { className: "justify-end", style: { marginTop: 8 }, children: [
            /* @__PURE__ */ jsx55(
              Button2,
              {
                type: "button",
                accessKey: "a",
                disabled: busy || !onAddHost,
                title: onAddHost ? "Add a new host" : "Opens Hosts \u2192 Add (coming soon)",
                onClick: onAddHost,
                children: "Add\u2026"
              }
            ),
            /* @__PURE__ */ jsx55(
              Button2,
              {
                type: "button",
                accessKey: "r",
                disabled: !canRemove,
                title: "Unassign selected host from this site",
                onClick: handleRemove,
                children: "Remove"
              }
            )
          ] })
        ] }) }) }),
        /* @__PURE__ */ jsxs32(FieldRow, { className: "justify-end site-form-dialog-actions", children: [
          /* @__PURE__ */ jsx55(Button2, { type: "submit", isDefault: true, accessKey: "o", loading: saving, children: "OK" }),
          /* @__PURE__ */ jsx55(Button2, { type: "button", accessKey: "c", disabled: busy, onClick: onClose, children: "Cancel" })
        ] })
      ] })
    }
  );
}

// src/admin/components/SitesWindow/SitesWindow.tsx
import { Fragment as Fragment9, jsx as jsx56, jsxs as jsxs33 } from "react/jsx-runtime";
function formatSaveErrors(formError, fieldErrors) {
  const parts = [
    formError,
    fieldErrors?.name,
    fieldErrors?.slug
  ].filter((part) => Boolean(part && part.trim()));
  if (parts.length === 0) {
    return null;
  }
  return [...new Set(parts)].join("\n");
}
function SitesWindow({
  sites = [],
  hosts = [],
  canEdit = false,
  loading = false,
  error = null,
  fieldErrors,
  formError = null,
  statusMessage = null,
  onClearStatusMessage,
  saving = false,
  deleting = false,
  onSave,
  onCreate,
  onDelete,
  onAddHost,
  onAssignHost,
  onUnassignHost,
  unassigning = false,
  assigning = false,
  errorSoundUrl,
  dingSoundUrl,
  onAlertClose,
  onCancel,
  onClose,
  onMinimize,
  onMaximize,
  onActivate,
  inactive = false,
  maximized = false,
  resizable = true,
  className,
  style,
  width = 560,
  tableMinHeight
}) {
  const [selectedId, setSelectedId] = useState10(null);
  const [form, setForm] = useState10({ open: false });
  const [showFormErrors, setShowFormErrors] = useState10(false);
  const [alert, setAlert] = useState10(null);
  const [confirmDelete, setConfirmDelete] = useState10(null);
  const wasSavingRef = useRef6(false);
  const alertSoundKeyRef = useRef6(null);
  const confirmSoundKeyRef = useRef6(null);
  const showErrorAlert = useCallback3(
    (message, title = "Error") => {
      const key = `${title}\0${message}`;
      setAlert({ title, message });
      if (alertSoundKeyRef.current === key) {
        return;
      }
      alertSoundKeyRef.current = key;
      playAdminSound("chord", errorSoundUrl);
    },
    [errorSoundUrl]
  );
  const closeAlert = useCallback3(() => {
    setAlert(null);
    alertSoundKeyRef.current = null;
    onAlertClose?.();
  }, [onAlertClose]);
  const openDeleteConfirm = useCallback3(
    (site) => {
      const key = `delete\0${site.id}`;
      setConfirmDelete({ site });
      if (confirmSoundKeyRef.current === key) {
        return;
      }
      confirmSoundKeyRef.current = key;
      playAdminSound("ding", dingSoundUrl);
    },
    [dingSoundUrl]
  );
  const closeDeleteConfirm = useCallback3(() => {
    setConfirmDelete(null);
    confirmSoundKeyRef.current = null;
  }, []);
  useEffect8(() => {
    if (selectedId != null && !sites.some((site) => site.id === selectedId)) {
      setSelectedId(null);
    }
  }, [sites, selectedId]);
  useEffect8(() => {
    if (confirmDelete != null && !sites.some((site) => site.id === confirmDelete.site.id)) {
      closeDeleteConfirm();
    }
  }, [sites, confirmDelete, closeDeleteConfirm]);
  useEffect8(() => {
    const hadErrors = Boolean(formError) || Boolean(fieldErrors && Object.keys(fieldErrors).length > 0);
    if (wasSavingRef.current && !saving && form.open && !hadErrors) {
      setForm({ open: false });
      setShowFormErrors(false);
    }
    wasSavingRef.current = saving;
  }, [saving, form.open, formError, fieldErrors]);
  useLayoutEffect3(() => {
    if (!error || loading) {
      return;
    }
    showErrorAlert(error);
  }, [error, loading, showErrorAlert]);
  useEffect8(() => {
    if (!formError && !(form.open && showFormErrors)) {
      return;
    }
    const message = formatSaveErrors(
      formError,
      form.open && showFormErrors ? fieldErrors : void 0
    );
    if (!message) {
      return;
    }
    showErrorAlert(message);
  }, [formError, fieldErrors, form.open, showFormErrors, showErrorAlert]);
  const busy = loading || saving || unassigning || assigning || deleting;
  const selected = sites.find((site) => site.id === selectedId) ?? null;
  const hasSelection = selected != null;
  const canSave = Boolean(onSave || onCreate);
  const selectSite = (id) => {
    onClearStatusMessage?.();
    setSelectedId((current) => current === id ? null : id);
  };
  const openNew = () => {
    if (!canEdit || busy) {
      return;
    }
    onClearStatusMessage?.();
    setShowFormErrors(false);
    setForm({
      open: true,
      mode: "new",
      name: "",
      slug: "",
      enabled: true
    });
  };
  const openEdit = (site) => {
    const target = site ?? selected;
    if (!canEdit || !target || busy || !canSave) {
      return;
    }
    if (selectedId !== target.id) {
      onClearStatusMessage?.();
    }
    setSelectedId(target.id);
    setShowFormErrors(false);
    setForm({
      open: true,
      mode: "edit",
      siteId: target.id,
      name: target.name,
      slug: target.slug,
      enabled: target.enabled,
      title: target.name
    });
  };
  const closeForm = () => {
    setShowFormErrors(false);
    setForm({ open: false });
  };
  const handleFormSave = (payload) => {
    setShowFormErrors(true);
    if (onSave) {
      onSave(payload);
      return;
    }
    if (payload.mode === "new" && onCreate) {
      onCreate({ name: payload.name, slug: payload.slug });
      setForm({ open: false });
    }
  };
  const handleDelete = () => {
    if (!canEdit || !selected || !onDelete || busy) {
      return;
    }
    openDeleteConfirm(selected);
  };
  const confirmDeleteSite = () => {
    if (!confirmDelete || !onDelete) {
      return;
    }
    const target = confirmDelete.site;
    closeDeleteConfirm();
    onDelete(target);
  };
  const handleCancel = () => {
    (onCancel ?? onClose)();
  };
  const statusLeft = loading ? "Loading\u2026" : `${sites.length} site${sites.length === 1 ? "" : "s"}`;
  const statusMid = statusMessage ?? (selected ? selected.name : canEdit ? "Select a site, or choose New." : "");
  return /* @__PURE__ */ jsx56(
    HeadingPanelWindow,
    {
      className: cn("sites-window", className),
      style: { width, minHeight: 420, ...style },
      inactive,
      resizable,
      title: "Sites",
      titleIcon: "sites",
      titleBarControls: /* @__PURE__ */ jsxs33(TitleBarControls, { children: [
        /* @__PURE__ */ jsx56(TitleBarControl, { action: "Minimize", onClick: onMinimize }),
        resizable ? /* @__PURE__ */ jsx56(
          TitleBarControl,
          {
            action: maximized ? "Restore" : "Maximize",
            onClick: onMaximize
          }
        ) : null,
        /* @__PURE__ */ jsx56(TitleBarControl, { action: "Close", onClick: onClose })
      ] }),
      onMouseDown: onActivate,
      heading: /* @__PURE__ */ jsx56("p", { style: { margin: 0 }, children: "Multi-tenant sites bound to one or more hostnames." }),
      actions: canEdit ? /* @__PURE__ */ jsxs33(FieldRow, { className: "justify-end", children: [
        /* @__PURE__ */ jsx56(
          Button2,
          {
            type: "button",
            isDefault: true,
            accessKey: "n",
            disabled: busy || !canSave,
            onClick: openNew,
            children: "New"
          }
        ),
        /* @__PURE__ */ jsx56(
          Button2,
          {
            type: "button",
            accessKey: "e",
            disabled: busy || !hasSelection || !canSave,
            onClick: () => openEdit(),
            children: "Edit"
          }
        ),
        /* @__PURE__ */ jsx56(
          Button2,
          {
            type: "button",
            accessKey: "d",
            disabled: busy || !hasSelection || !onDelete,
            onClick: handleDelete,
            children: "Delete"
          }
        ),
        /* @__PURE__ */ jsx56(Button2, { type: "button", accessKey: "c", disabled: busy, onClick: handleCancel, children: "Cancel" })
      ] }) : /* @__PURE__ */ jsx56(FieldRow, { className: "justify-end", children: /* @__PURE__ */ jsx56(Button2, { type: "button", accessKey: "c", onClick: handleCancel, children: "Cancel" }) }),
      statusBar: /* @__PURE__ */ jsxs33(StatusBar, { children: [
        /* @__PURE__ */ jsx56(StatusBarField, { children: statusLeft }),
        /* @__PURE__ */ jsx56(StatusBarField, { className: "description", children: statusMid }),
        /* @__PURE__ */ jsx56(StatusBarField, {})
      ] }),
      children: /* @__PURE__ */ jsxs33(Fragment9, { children: [
        /* @__PURE__ */ jsx56(
          SunkenPanel,
          {
            scrollable: true,
            tone: "white",
            style: tableMinHeight != null ? { minHeight: tableMinHeight } : void 0,
            children: loading && sites.length === 0 ? /* @__PURE__ */ jsx56("p", { style: { margin: 8 }, children: "Loading sites\u2026" }) : sites.length === 0 ? /* @__PURE__ */ jsx56("p", { style: { margin: 8 }, children: "No sites yet." }) : /* @__PURE__ */ jsxs33(Table, { "aria-label": "Sites", children: [
              /* @__PURE__ */ jsx56("thead", { children: /* @__PURE__ */ jsxs33("tr", { children: [
                /* @__PURE__ */ jsx56("th", { children: "Name" }),
                /* @__PURE__ */ jsx56("th", { children: "Slug" }),
                /* @__PURE__ */ jsx56("th", { children: "Hosts" }),
                /* @__PURE__ */ jsx56("th", { children: "Status" })
              ] }) }),
              /* @__PURE__ */ jsx56("tbody", { children: sites.map((site) => /* @__PURE__ */ jsxs33(
                TableRow,
                {
                  highlighted: selectedId === site.id,
                  onClick: () => selectSite(site.id),
                  onDoubleClick: () => openEdit(site),
                  children: [
                    /* @__PURE__ */ jsx56("td", { children: site.name }),
                    /* @__PURE__ */ jsx56("td", { children: site.slug }),
                    /* @__PURE__ */ jsx56("td", { children: site.hostCount }),
                    /* @__PURE__ */ jsx56("td", { children: site.enabled ? "Enabled" : "Disabled" })
                  ]
                },
                site.id
              )) })
            ] })
          }
        ),
        form.open ? /* @__PURE__ */ jsx56(DesktopModal, { dingSoundUrl, children: /* @__PURE__ */ jsx56(
          SiteFormDialog,
          {
            mode: form.mode,
            initial: {
              siteId: form.siteId,
              name: form.name,
              slug: form.slug,
              enabled: form.enabled,
              title: form.title
            },
            hosts,
            fieldErrors: showFormErrors ? fieldErrors : void 0,
            saving,
            unassigning,
            assigning,
            onSave: handleFormSave,
            onError: showErrorAlert,
            onClose: closeForm,
            onAddHost,
            onAssignHost: onAssignHost && form.open && form.siteId != null ? (hostId) => onAssignHost(hostId, form.siteId) : void 0,
            onUnassignHost
          },
          `${form.mode}-${form.siteId ?? "new"}`
        ) }) : null,
        confirmDelete ? /* @__PURE__ */ jsx56(DesktopModal, { layer: "alert", dingSoundUrl, children: /* @__PURE__ */ jsx56(
          MessageDialog,
          {
            type: "question",
            title: "Confirm",
            message: `Delete site \u201C${confirmDelete.site.name}\u201D? This cannot be undone.`,
            onClose: closeDeleteConfirm,
            onConfirm: confirmDeleteSite
          }
        ) }) : null,
        alert ? /* @__PURE__ */ jsx56(DesktopModal, { layer: "alert", dingSoundUrl, children: /* @__PURE__ */ jsx56(
          MessageDialog,
          {
            type: "error",
            title: alert.title,
            message: alert.message,
            onClose: closeAlert
          }
        ) }) : null
      ] })
    }
  );
}

// src/admin/components/HostsWindow/HostsWindow.tsx
import { useCallback as useCallback4, useEffect as useEffect10, useLayoutEffect as useLayoutEffect4, useRef as useRef7, useState as useState12 } from "react";

// src/admin/components/HostsWindow/HostFormDialog.tsx
import { useEffect as useEffect9, useId as useId3, useState as useState11 } from "react";
import { jsx as jsx57, jsxs as jsxs34 } from "react/jsx-runtime";
function HostFormDialog({
  mode,
  initial,
  sites = [],
  fieldErrors,
  saving = false,
  onSave,
  onError,
  onClose,
  className
}) {
  const hostId = useId3();
  const siteSelectId = useId3();
  const surfaceId = useId3();
  const enabledId = useId3();
  const [host, setHost] = useState11(initial?.host ?? "");
  const [siteId, setSiteId] = useState11(initial?.siteId ?? null);
  const [surface, setSurface] = useState11(initial?.surface ?? "site");
  const [enabled, setEnabled] = useState11(initial?.enabled ?? true);
  const [localErrors, setLocalErrors] = useState11({});
  useEffect9(() => {
    setLocalErrors({});
  }, [fieldErrors]);
  const errors = { ...localErrors, ...fieldErrors };
  const title = mode === "new" ? "New Host" : `${initial?.title ?? initial?.host ?? "Host"} Properties`;
  const siteSelectLocked = mode === "new" || mode === "edit" && initial?.verification === "pending";
  const handleSubmit = (event) => {
    event.preventDefault();
    if (saving) {
      return;
    }
    const nextHost = host.trim().toLowerCase();
    const nextLocal = {};
    if (!nextHost) {
      nextLocal.host = "Hostname is required.";
    } else if (!/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/.test(nextHost)) {
      nextLocal.host = "Use a valid domain name.";
    }
    setLocalErrors(nextLocal);
    if (Object.keys(nextLocal).length > 0) {
      onError?.(Object.values(nextLocal).join("\n"));
      return;
    }
    onSave({
      mode,
      hostId: initial?.hostId,
      host: nextHost,
      // New hosts stay unassigned; assign after verify.
      siteId: mode === "new" ? null : siteId,
      surface,
      enabled
    });
  };
  return /* @__PURE__ */ jsx57(
    PaneWindowShell,
    {
      className: cn("host-form-dialog", "site-form-dialog", className),
      width: 420,
      title,
      titleIcon: "hosts",
      titleBarControls: /* @__PURE__ */ jsx57(TitleBarControls, { children: /* @__PURE__ */ jsx57(TitleBarControl, { action: "Close", onClick: onClose }) }),
      children: /* @__PURE__ */ jsxs34("form", { className: "site-form-dialog-form", onSubmit: handleSubmit, noValidate: true, children: [
        /* @__PURE__ */ jsxs34(WindowBody, { children: [
          /* @__PURE__ */ jsx57(FieldRow, { children: /* @__PURE__ */ jsx57(
            TextBox,
            {
              id: hostId,
              label: "Host:",
              accessKey: "h",
              value: host,
              disabled: saving,
              "aria-invalid": Boolean(errors.host) || void 0,
              onChange: (event) => setHost(event.target.value)
            }
          ) }),
          /* @__PURE__ */ jsx57(FieldRow, { children: /* @__PURE__ */ jsxs34(
            Select2,
            {
              id: siteSelectId,
              label: "Site:",
              accessKey: "s",
              value: siteId != null ? String(siteId) : "",
              disabled: saving || siteSelectLocked,
              title: mode === "new" ? "Assign a site after ownership is verified" : siteSelectLocked ? "Verify ownership before assigning a site" : void 0,
              "aria-invalid": Boolean(errors.siteId) || void 0,
              onChange: (event) => {
                const value = event.target.value;
                setSiteId(value === "" ? null : Number(value));
              },
              children: [
                /* @__PURE__ */ jsx57("option", { value: "", children: "None" }),
                sites.map((site) => /* @__PURE__ */ jsx57("option", { value: site.id, children: site.name }, site.id))
              ]
            }
          ) }),
          /* @__PURE__ */ jsx57(FieldRow, { children: /* @__PURE__ */ jsxs34(
            Select2,
            {
              id: surfaceId,
              label: "Surface:",
              accessKey: "u",
              value: surface,
              disabled: saving,
              "aria-invalid": Boolean(errors.surface) || void 0,
              onChange: (event) => setSurface(event.target.value),
              children: [
                /* @__PURE__ */ jsx57("option", { value: "site", children: "site" }),
                /* @__PURE__ */ jsx57("option", { value: "admin", children: "admin" }),
                /* @__PURE__ */ jsx57("option", { value: "api", children: "api" })
              ]
            }
          ) }),
          /* @__PURE__ */ jsx57(FieldRow, { children: /* @__PURE__ */ jsx57(
            Checkbox,
            {
              id: enabledId,
              label: "Enabled",
              accessKey: "e",
              checked: enabled,
              disabled: saving,
              "aria-invalid": Boolean(errors.enabled) || void 0,
              onChange: (event) => setEnabled(event.target.checked)
            }
          ) })
        ] }),
        /* @__PURE__ */ jsxs34(FieldRow, { className: "justify-end site-form-dialog-actions", children: [
          /* @__PURE__ */ jsx57(Button2, { type: "submit", isDefault: true, accessKey: "o", loading: saving, children: "OK" }),
          /* @__PURE__ */ jsx57(Button2, { type: "button", accessKey: "c", disabled: saving, onClick: onClose, children: "Cancel" })
        ] })
      ] })
    }
  );
}

// src/admin/components/HostsWindow/HostsWindow.tsx
import { Fragment as Fragment10, jsx as jsx58, jsxs as jsxs35 } from "react/jsx-runtime";
function formatSaveErrors2(formError, fieldErrors) {
  const parts = [
    formError,
    fieldErrors?.host,
    fieldErrors?.siteId,
    fieldErrors?.surface,
    fieldErrors?.enabled
  ].filter((part) => Boolean(part && part.trim()));
  if (parts.length === 0) {
    return null;
  }
  return [...new Set(parts)].join("\n");
}
function HostsWindow({
  hosts = [],
  sites = [],
  canEdit = false,
  loading = false,
  error = null,
  fieldErrors,
  formError = null,
  statusMessage = null,
  onClearStatusMessage,
  saving = false,
  deleting = false,
  verifying = false,
  onSave,
  onVerify,
  onDelete,
  errorSoundUrl,
  dingSoundUrl,
  onAlertClose,
  onClearFormError,
  onCancel,
  onClose,
  onMinimize,
  onMaximize,
  onActivate,
  inactive = false,
  maximized = false,
  resizable = true,
  className,
  style,
  width = 640,
  tableMinHeight
}) {
  const [selectedId, setSelectedId] = useState12(null);
  const [form, setForm] = useState12({ open: false });
  const [showFormErrors, setShowFormErrors] = useState12(false);
  const [alert, setAlert] = useState12(null);
  const [confirmDelete, setConfirmDelete] = useState12(null);
  const wasSavingRef = useRef7(false);
  const alertSoundKeyRef = useRef7(null);
  const confirmSoundKeyRef = useRef7(null);
  const showErrorAlert = useCallback4(
    (message, title = "Error") => {
      const key = `${title}\0${message}`;
      setAlert({ title, message });
      if (alertSoundKeyRef.current === key) {
        return;
      }
      alertSoundKeyRef.current = key;
      playAdminSound("chord", errorSoundUrl);
    },
    [errorSoundUrl]
  );
  const closeAlert = useCallback4(() => {
    setAlert(null);
    alertSoundKeyRef.current = null;
    onClearFormError?.();
    onAlertClose?.();
  }, [onAlertClose, onClearFormError]);
  const openDeleteConfirm = useCallback4(
    (host) => {
      const key = `delete\0${host.id}`;
      setConfirmDelete({ host });
      if (confirmSoundKeyRef.current === key) {
        return;
      }
      confirmSoundKeyRef.current = key;
      playAdminSound("ding", dingSoundUrl);
    },
    [dingSoundUrl]
  );
  const closeDeleteConfirm = useCallback4(() => {
    setConfirmDelete(null);
    confirmSoundKeyRef.current = null;
  }, []);
  useEffect10(() => {
    if (selectedId != null && !hosts.some((row) => row.id === selectedId)) {
      setSelectedId(null);
    }
  }, [hosts, selectedId]);
  useEffect10(() => {
    if (confirmDelete != null && !hosts.some((row) => row.id === confirmDelete.host.id)) {
      closeDeleteConfirm();
    }
  }, [hosts, confirmDelete, closeDeleteConfirm]);
  useEffect10(() => {
    const hadErrors = Boolean(formError) || Boolean(fieldErrors && Object.keys(fieldErrors).length > 0);
    if (wasSavingRef.current && !saving && form.open && !hadErrors) {
      setForm({ open: false });
      setShowFormErrors(false);
    }
    wasSavingRef.current = saving;
  }, [saving, form.open, formError, fieldErrors]);
  useLayoutEffect4(() => {
    if (!error || loading) {
      return;
    }
    showErrorAlert(error);
  }, [error, loading, showErrorAlert]);
  useEffect10(() => {
    if (!formError && !(form.open && showFormErrors)) {
      return;
    }
    const message = formatSaveErrors2(
      formError,
      form.open && showFormErrors ? fieldErrors : void 0
    );
    if (!message) {
      return;
    }
    showErrorAlert(message);
  }, [formError, fieldErrors, form.open, showFormErrors, showErrorAlert]);
  const busy = loading || saving || verifying || deleting;
  const selected = hosts.find((row) => row.id === selectedId) ?? null;
  const hasSelection = selected != null;
  const canSave = Boolean(onSave);
  const canVerifySelected = Boolean(onVerify) && selected?.verification === "pending" && !busy;
  const selectHost = (id) => {
    onClearStatusMessage?.();
    onClearFormError?.();
    setSelectedId((current) => current === id ? null : id);
  };
  const openNew = () => {
    if (!canEdit || busy) {
      return;
    }
    onClearStatusMessage?.();
    onClearFormError?.();
    setShowFormErrors(false);
    setForm({
      open: true,
      mode: "new",
      host: "",
      siteId: null,
      surface: "site",
      enabled: true
    });
  };
  const openEdit = (row) => {
    const target = row ?? selected;
    if (!canEdit || !target || busy || !canSave) {
      return;
    }
    onClearFormError?.();
    if (selectedId !== target.id) {
      onClearStatusMessage?.();
    }
    setSelectedId(target.id);
    setShowFormErrors(false);
    setForm({
      open: true,
      mode: "edit",
      hostId: target.id,
      host: target.host,
      siteId: target.siteId,
      surface: target.surface,
      enabled: target.enabled,
      verification: target.verification,
      title: target.host
    });
  };
  const closeForm = () => {
    setShowFormErrors(false);
    setForm({ open: false });
    onClearFormError?.();
  };
  const handleFormSave = (payload) => {
    setShowFormErrors(true);
    onSave?.(payload);
  };
  const handleDelete = () => {
    if (!canEdit || !selected || !onDelete || busy) {
      return;
    }
    openDeleteConfirm(selected);
  };
  const confirmDeleteHost = () => {
    if (!confirmDelete || !onDelete) {
      return;
    }
    const target = confirmDelete.host;
    closeDeleteConfirm();
    onDelete(target);
  };
  const handleVerify = () => {
    if (!canVerifySelected || !selected || !onVerify) {
      return;
    }
    onClearStatusMessage?.();
    onVerify(selected);
  };
  const handleCancel = () => {
    (onCancel ?? onClose)();
  };
  const statusLeft = loading ? "Loading\u2026" : `${hosts.length} host${hosts.length === 1 ? "" : "s"}`;
  const statusMid = statusMessage ?? (selected ? selected.host : canEdit ? "Select a host, or choose New." : "");
  return /* @__PURE__ */ jsx58(
    HeadingPanelWindow,
    {
      className: cn("hosts-window", className),
      style: { width, minHeight: 420, ...style },
      inactive,
      resizable,
      title: "Hosts",
      titleIcon: "hosts",
      titleBarControls: /* @__PURE__ */ jsxs35(TitleBarControls, { children: [
        /* @__PURE__ */ jsx58(TitleBarControl, { action: "Minimize", onClick: onMinimize }),
        resizable ? /* @__PURE__ */ jsx58(
          TitleBarControl,
          {
            action: maximized ? "Restore" : "Maximize",
            onClick: onMaximize
          }
        ) : null,
        /* @__PURE__ */ jsx58(TitleBarControl, { action: "Close", onClick: onClose })
      ] }),
      onMouseDown: onActivate,
      heading: /* @__PURE__ */ jsx58("p", { style: { margin: 0 }, children: "Domain names bound to sites (admin, site, or API surfaces)." }),
      actions: canEdit ? /* @__PURE__ */ jsxs35(FieldRow, { className: "justify-end", children: [
        /* @__PURE__ */ jsx58(
          Button2,
          {
            type: "button",
            isDefault: true,
            accessKey: "n",
            disabled: busy || !canSave,
            onClick: openNew,
            children: "New"
          }
        ),
        /* @__PURE__ */ jsx58(
          Button2,
          {
            type: "button",
            accessKey: "e",
            disabled: busy || !hasSelection || !canSave,
            onClick: () => openEdit(),
            children: "Edit"
          }
        ),
        /* @__PURE__ */ jsx58(
          Button2,
          {
            type: "button",
            accessKey: "v",
            disabled: !canVerifySelected,
            title: selected?.verification === "pending" ? "Verify hostname ownership" : "Select a pending host to verify",
            onClick: handleVerify,
            children: "Verify"
          }
        ),
        /* @__PURE__ */ jsx58(
          Button2,
          {
            type: "button",
            accessKey: "d",
            disabled: busy || !hasSelection || !onDelete,
            onClick: handleDelete,
            children: "Delete"
          }
        ),
        /* @__PURE__ */ jsx58(Button2, { type: "button", accessKey: "c", disabled: busy, onClick: handleCancel, children: "Cancel" })
      ] }) : /* @__PURE__ */ jsx58(FieldRow, { className: "justify-end", children: /* @__PURE__ */ jsx58(Button2, { type: "button", accessKey: "c", onClick: handleCancel, children: "Cancel" }) }),
      statusBar: /* @__PURE__ */ jsxs35(StatusBar, { children: [
        /* @__PURE__ */ jsx58(StatusBarField, { children: statusLeft }),
        /* @__PURE__ */ jsx58(StatusBarField, { className: "description", children: statusMid }),
        /* @__PURE__ */ jsx58(StatusBarField, {})
      ] }),
      children: /* @__PURE__ */ jsxs35(Fragment10, { children: [
        /* @__PURE__ */ jsx58(
          SunkenPanel,
          {
            scrollable: true,
            tone: "white",
            style: tableMinHeight != null ? { minHeight: tableMinHeight } : void 0,
            children: loading && hosts.length === 0 ? /* @__PURE__ */ jsx58("p", { style: { margin: 8 }, children: "Loading hosts\u2026" }) : hosts.length === 0 ? /* @__PURE__ */ jsx58("p", { style: { margin: 8 }, children: "No hosts yet." }) : /* @__PURE__ */ jsxs35(Table, { "aria-label": "Hosts", children: [
              /* @__PURE__ */ jsx58("thead", { children: /* @__PURE__ */ jsxs35("tr", { children: [
                /* @__PURE__ */ jsx58("th", { children: "Hostname" }),
                /* @__PURE__ */ jsx58("th", { children: "Site" }),
                /* @__PURE__ */ jsx58("th", { children: "Surface" }),
                /* @__PURE__ */ jsx58("th", { children: "Verification" }),
                /* @__PURE__ */ jsx58("th", { children: "Status" })
              ] }) }),
              /* @__PURE__ */ jsx58("tbody", { children: hosts.map((row) => /* @__PURE__ */ jsxs35(
                TableRow,
                {
                  highlighted: selectedId === row.id,
                  onClick: () => selectHost(row.id),
                  onDoubleClick: () => openEdit(row),
                  children: [
                    /* @__PURE__ */ jsx58("td", { children: row.host }),
                    /* @__PURE__ */ jsx58("td", { children: row.siteName?.trim() ? row.siteName : "\u2014" }),
                    /* @__PURE__ */ jsx58("td", { children: row.surface }),
                    /* @__PURE__ */ jsx58("td", { children: row.verification }),
                    /* @__PURE__ */ jsx58("td", { children: row.enabled ? "Enabled" : "Disabled" })
                  ]
                },
                row.id
              )) })
            ] })
          }
        ),
        form.open ? /* @__PURE__ */ jsx58(DesktopModal, { dingSoundUrl, children: /* @__PURE__ */ jsx58(
          HostFormDialog,
          {
            mode: form.mode,
            initial: {
              hostId: form.hostId,
              host: form.host,
              siteId: form.siteId,
              surface: form.surface,
              enabled: form.enabled,
              verification: form.verification,
              title: form.title
            },
            sites,
            fieldErrors: showFormErrors ? fieldErrors : void 0,
            saving,
            onSave: handleFormSave,
            onError: showErrorAlert,
            onClose: closeForm
          },
          `${form.mode}-${form.hostId ?? "new"}`
        ) }) : null,
        confirmDelete ? /* @__PURE__ */ jsx58(DesktopModal, { layer: "alert", dingSoundUrl, children: /* @__PURE__ */ jsx58(
          MessageDialog,
          {
            type: "question",
            title: "Confirm",
            message: `Delete host \u201C${confirmDelete.host.host}\u201D? This cannot be undone.`,
            onClose: closeDeleteConfirm,
            onConfirm: confirmDeleteHost
          }
        ) }) : null,
        alert ? /* @__PURE__ */ jsx58(DesktopModal, { layer: "alert", dingSoundUrl, children: /* @__PURE__ */ jsx58(
          MessageDialog,
          {
            type: "error",
            title: alert.title,
            message: alert.message,
            onClose: closeAlert
          }
        ) }) : null
      ] })
    }
  );
}

// src/admin/views/SiteListView.tsx
import { jsx as jsx59, jsxs as jsxs36 } from "react/jsx-runtime";
function SiteListView({
  sites,
  loading = false,
  createHref = "/admin/sites/new",
  editHref = (site) => `/admin/sites/${site.id}`
}) {
  return /* @__PURE__ */ jsxs36("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx59(
      PageHeader,
      {
        title: "Sites",
        description: "Multi-tenant sites bound to one or more hostnames.",
        actions: /* @__PURE__ */ jsx59("a", { href: createHref, children: /* @__PURE__ */ jsx59(Button, { children: "New site" }) })
      }
    ),
    /* @__PURE__ */ jsx59(
      DataTable,
      {
        loading,
        rows: sites,
        rowKey: (row) => row.id,
        emptyMessage: "No sites yet. Create the first tenant.",
        columns: [
          { key: "name", header: "Name", render: (row) => row.name },
          { key: "slug", header: "Slug", render: (row) => /* @__PURE__ */ jsx59("code", { children: row.slug }) },
          {
            key: "hosts",
            header: "Hosts",
            render: (row) => String(row.hostCount)
          },
          {
            key: "status",
            header: "Status",
            render: (row) => /* @__PURE__ */ jsx59(Badge, { tone: row.enabled ? "success" : "neutral", children: row.enabled ? "Enabled" : "Disabled" })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx59("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/SiteHostListView.tsx
import { jsx as jsx60, jsxs as jsxs37 } from "react/jsx-runtime";
var verificationTone = {
  pending: "warning",
  verified: "accent"
};
function SiteHostListView({
  hosts,
  loading = false,
  createHref = "/admin/hosts/new",
  verifyHref = (host) => `/admin/hosts/${host.id}/verify`
}) {
  return /* @__PURE__ */ jsxs37("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx60(
      PageHeader,
      {
        title: "Hosts",
        description: "Domain names mapped to sites and surfaces (admin, site, api).",
        actions: /* @__PURE__ */ jsx60("a", { href: createHref, children: /* @__PURE__ */ jsx60(Button, { children: "Add host" }) })
      }
    ),
    /* @__PURE__ */ jsx60(
      DataTable,
      {
        loading,
        rows: hosts,
        rowKey: (row) => row.id,
        emptyMessage: "No hosts configured.",
        columns: [
          { key: "host", header: "Hostname", render: (row) => /* @__PURE__ */ jsx60("code", { children: row.host }) },
          { key: "site", header: "Site", render: (row) => row.siteName },
          {
            key: "surface",
            header: "Surface",
            render: (row) => /* @__PURE__ */ jsx60(Badge, { tone: "accent", children: row.surface })
          },
          {
            key: "verification",
            header: "Verification",
            render: (row) => /* @__PURE__ */ jsx60(Badge, { tone: verificationTone[row.verification], children: row.verification })
          },
          {
            key: "status",
            header: "Status",
            render: (row) => row.enabled ? "Enabled" : "Disabled"
          },
          {
            key: "actions",
            header: "",
            render: (row) => row.verification === "pending" ? /* @__PURE__ */ jsx60("a", { href: verifyHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Verify" }) : "\u2014"
          }
        ]
      }
    )
  ] });
}

// src/admin/views/UserListView.tsx
import { jsx as jsx61, jsxs as jsxs38 } from "react/jsx-runtime";
function UserListView({
  users,
  loading = false,
  createHref = "/admin/users/new",
  editHref = (user) => `/admin/users/${user.id}`
}) {
  return /* @__PURE__ */ jsxs38("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx61(
      PageHeader,
      {
        title: "Users",
        description: "Accounts with global roles and optional per-site assignments.",
        actions: /* @__PURE__ */ jsx61("a", { href: createHref, children: /* @__PURE__ */ jsx61(Button, { children: "New user" }) })
      }
    ),
    /* @__PURE__ */ jsx61(
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
            render: (row) => /* @__PURE__ */ jsx61("div", { className: "flex flex-wrap gap-1", children: row.roles.map((role) => /* @__PURE__ */ jsx61(Badge, { children: role }, role)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx61("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/RoleListView.tsx
import { jsx as jsx62, jsxs as jsxs39 } from "react/jsx-runtime";
function RoleListView({
  roles,
  loading = false,
  createHref = "/admin/roles/new",
  editHref = (role) => `/admin/roles/${role.id}`
}) {
  return /* @__PURE__ */ jsxs39("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx62(
      PageHeader,
      {
        title: "Roles & permissions",
        description: "RBAC roles with permission strings such as site.list.",
        actions: /* @__PURE__ */ jsx62("a", { href: createHref, children: /* @__PURE__ */ jsx62(Button, { children: "New role" }) })
      }
    ),
    /* @__PURE__ */ jsx62(
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
            render: (row) => /* @__PURE__ */ jsx62("div", { className: "flex flex-wrap gap-1", children: row.permissions.map((permission) => /* @__PURE__ */ jsx62(Badge, { tone: "accent", children: permission }, permission)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx62("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/pages/LoginPage.tsx
import { useEffect as useEffect11, useLayoutEffect as useLayoutEffect5, useRef as useRef8, useState as useState13 } from "react";
import { jsx as jsx63, jsxs as jsxs40 } from "react/jsx-runtime";
function normalizeError(error) {
  if (typeof error === "string") {
    const trimmed = error.trim();
    return trimmed.length > 0 ? trimmed : null;
  }
  if (error && typeof error === "object" && "message" in error) {
    const message = error.message;
    if (typeof message === "string" && message.trim()) {
      return message.trim();
    }
  }
  return null;
}
function LoginPage({
  action,
  csrfToken,
  csrfFieldName,
  emailDefault,
  error,
  bannerUrl,
  errorSoundUrl,
  dingSoundUrl
}) {
  const dashboardRef = useRef8(null);
  const modalRootRef = useRef8(null);
  const soundedFor = useRef8(null);
  const message = normalizeError(error);
  const [dismissed, setDismissed] = useState13(false);
  const [boundsEl, setBoundsEl] = useState13(null);
  const showAlert = Boolean(message && !dismissed);
  useLayoutEffect5(() => {
    setBoundsEl(dashboardRef.current);
  }, []);
  useEffect11(() => {
    setDismissed(false);
  }, [message]);
  useEffect11(() => {
    if (!message || dismissed) {
      if (!message) {
        soundedFor.current = null;
      }
      return;
    }
    if (soundedFor.current === message) {
      return;
    }
    soundedFor.current = message;
    playAdminSound("chord", errorSoundUrl);
  }, [message, dismissed, errorSoundUrl]);
  return /* @__PURE__ */ jsxs40("div", { ref: dashboardRef, className: "dashboard login-desktop", children: [
    /* @__PURE__ */ jsxs40("div", { className: "login-host", children: [
      /* @__PURE__ */ jsx63(
        LoginForm,
        {
          action,
          csrfToken,
          csrfFieldName,
          emailDefault: emailDefault || "",
          bannerUrl
        }
      ),
      showAlert ? /* @__PURE__ */ jsx63(
        "div",
        {
          className: "modal-blocker",
          "aria-hidden": true,
          onPointerDown: (event) => {
            if (event.button !== 0) {
              return;
            }
            event.preventDefault();
            event.stopPropagation();
            flashOwnedModalAttention(modalRootRef.current, { dingSoundUrl });
          }
        }
      ) : null
    ] }),
    showAlert ? /* @__PURE__ */ jsx63("div", { className: "desktop-modal-layer is-alert", children: /* @__PURE__ */ jsx63(FloatingModal, { boundsEl, rootRef: modalRootRef, children: /* @__PURE__ */ jsx63(
      MessageDialog,
      {
        type: "error",
        title: "Error",
        message,
        onClose: () => setDismissed(true)
      }
    ) }) }) : null
  ] });
}

// src/admin/pages/AdminDesktop.tsx
import { useCallback as useCallback5, useEffect as useEffect15, useMemo as useMemo5, useRef as useRef11, useState as useState15 } from "react";

// src/admin/shell/types.ts
var CONTROL_PANEL_WINDOW_ID = "control-panel";
var SITES_WINDOW_ID = "sites";
var HOSTS_WINDOW_ID = "hosts";
function siteWindowId(siteId) {
  return `site-${siteId}`;
}
function parseSiteWindowId(id) {
  const match = /^site-(\d+)$/.exec(id);
  return match ? Number(match[1]) : null;
}

// src/admin/shell/DesktopWindow.tsx
import {
  useEffect as useEffect12,
  useRef as useRef9
} from "react";

// src/admin/shell/resize.ts
var RESIZE_EDGES = ["e", "w", "s", "se", "sw"];
var RESIZE_CURSORS = {
  e: "ew-resize",
  w: "ew-resize",
  s: "ns-resize",
  se: "nwse-resize",
  sw: "nesw-resize"
};
var SHELL_MIN_WIDTH = 500;
var SHELL_MIN_HEIGHT = 340;
var DEFAULT_WINDOW_SIZE = {
  "control-panel": { width: 600, height: 380 },
  site: { width: 640, height: 440 },
  sites: { width: 560, height: 480 },
  hosts: { width: 640, height: 480 }
};
function computeResizeBounds(dashboard, edge, start, pointer, minWidth = SHELL_MIN_WIDTH, minHeight = SHELL_MIN_HEIGHT) {
  const dx = pointer.clientX - pointer.startX;
  const dy = pointer.clientY - pointer.startY;
  let nextLeft = start.left;
  let nextTop = start.top;
  let nextWidth = start.width;
  let nextHeight = start.height;
  if (edge.includes("e")) {
    nextWidth = start.width + dx;
  }
  if (edge.includes("w")) {
    nextWidth = start.width - dx;
    nextLeft = start.left + dx;
  }
  if (edge.includes("s")) {
    nextHeight = start.height + dy;
  }
  if (nextWidth < minWidth) {
    if (edge.includes("w")) {
      nextLeft = start.left + (start.width - minWidth);
    }
    nextWidth = minWidth;
  }
  if (nextHeight < minHeight) {
    nextHeight = minHeight;
  }
  const work = getDesktopWorkSize(dashboard);
  const maxWidth = Math.max(minWidth, work.width - nextLeft);
  const maxHeight = Math.max(minHeight, work.height - nextTop);
  nextWidth = Math.min(nextWidth, maxWidth);
  nextHeight = Math.min(nextHeight, maxHeight);
  if (edge.includes("w")) {
    const maxLeft = start.left + start.width - minWidth;
    nextLeft = Math.max(0, Math.min(nextLeft, maxLeft));
    nextWidth = start.left + start.width - nextLeft;
    nextWidth = Math.min(nextWidth, work.width - nextLeft);
  }
  nextLeft = Math.max(0, Math.min(nextLeft, Math.max(0, work.width - nextWidth)));
  nextTop = Math.max(0, Math.min(nextTop, Math.max(0, work.height - nextHeight)));
  return {
    left: nextLeft,
    top: nextTop,
    width: nextWidth,
    height: nextHeight
  };
}

// src/admin/shell/DesktopWindow.tsx
import { jsx as jsx64, jsxs as jsxs41 } from "react/jsx-runtime";
function DesktopWindow({
  windowId,
  left,
  top,
  zIndex,
  width,
  height,
  maximized = false,
  onPositionChange,
  onBoundsChange,
  onActivate,
  onToggleMaximize,
  dragDisabled = false,
  resizable = true,
  className,
  children,
  style,
  onPointerDown,
  ...rest
}) {
  const rootRef = useRef9(null);
  const dragRef = useRef9(null);
  const resizeRef = useRef9(null);
  const onPositionChangeRef = useRef9(onPositionChange);
  const onBoundsChangeRef = useRef9(onBoundsChange);
  const dragDisabledRef = useRef9(dragDisabled);
  const maximizedRef = useRef9(maximized);
  useEffect12(() => {
    onPositionChangeRef.current = onPositionChange;
  }, [onPositionChange]);
  useEffect12(() => {
    onBoundsChangeRef.current = onBoundsChange;
  }, [onBoundsChange]);
  useEffect12(() => {
    dragDisabledRef.current = dragDisabled;
  }, [dragDisabled]);
  useEffect12(() => {
    maximizedRef.current = maximized;
  }, [maximized]);
  useEffect12(() => {
    const onMove = (event) => {
      const node = rootRef.current;
      if (!node) {
        return;
      }
      const resize = resizeRef.current;
      if (resize && event.pointerId === resize.pointerId) {
        const dashboard2 = node.closest(".dashboard");
        const change2 = onBoundsChangeRef.current;
        if (!(dashboard2 instanceof HTMLElement) || !change2) {
          return;
        }
        const next2 = computeResizeBounds(dashboard2, resize.edge, resize.start, {
          clientX: event.clientX,
          clientY: event.clientY,
          startX: resize.startX,
          startY: resize.startY
        });
        change2(next2);
        event.preventDefault();
        return;
      }
      const session = dragRef.current;
      if (!session || event.pointerId !== session.pointerId) {
        return;
      }
      if (dragDisabledRef.current || maximizedRef.current) {
        return;
      }
      if (!session.active) {
        const dx = event.clientX - session.startX;
        const dy = event.clientY - session.startY;
        if (dx * dx + dy * dy < DRAG_THRESHOLD_PX * DRAG_THRESHOLD_PX) {
          return;
        }
        session.active = true;
        node.classList.add("is-dragging");
        try {
          if (typeof node.setPointerCapture === "function") {
            node.setPointerCapture(session.pointerId);
          }
        } catch {
        }
        event.preventDefault();
      }
      const dashboard = node.closest(".dashboard");
      const change = onPositionChangeRef.current;
      if (!(dashboard instanceof HTMLElement) || !change) {
        return;
      }
      const origin = dashboard.getBoundingClientRect();
      const next = clampDesktopPosition(
        dashboard,
        node.offsetWidth,
        node.offsetHeight,
        event.clientX - origin.left - session.offsetX,
        event.clientY - origin.top - session.offsetY
      );
      change(next.left, next.top);
    };
    const onUp = (event) => {
      const node = rootRef.current;
      const resize = resizeRef.current;
      if (resize && event.pointerId === resize.pointerId) {
        if (node && typeof node.releasePointerCapture === "function" && node.hasPointerCapture?.(resize.pointerId)) {
          node.releasePointerCapture(resize.pointerId);
        }
        node?.classList.remove("is-resizing");
        document.documentElement.classList.remove("is-window-resizing");
        document.documentElement.style.removeProperty("cursor");
        resizeRef.current = null;
        return;
      }
      const session = dragRef.current;
      if (!session || event.pointerId !== session.pointerId) {
        return;
      }
      if (node && typeof node.releasePointerCapture === "function" && node.hasPointerCapture?.(session.pointerId)) {
        node.releasePointerCapture(session.pointerId);
      }
      node?.classList.remove("is-dragging");
      dragRef.current = null;
    };
    window.addEventListener("pointermove", onMove);
    window.addEventListener("pointerup", onUp);
    window.addEventListener("pointercancel", onUp);
    return () => {
      window.removeEventListener("pointermove", onMove);
      window.removeEventListener("pointerup", onUp);
      window.removeEventListener("pointercancel", onUp);
    };
  }, []);
  const startResize = (edge, event) => {
    if (!onBoundsChange || maximized || event.button !== 0 || !rootRef.current) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    onActivate?.();
    const node = rootRef.current;
    resizeRef.current = {
      pointerId: event.pointerId,
      edge,
      startX: event.clientX,
      startY: event.clientY,
      start: {
        left,
        top,
        width: width ?? node.offsetWidth,
        height: height ?? node.offsetHeight
      }
    };
    node.classList.add("is-resizing");
    document.documentElement.classList.add("is-window-resizing");
    document.documentElement.style.setProperty(
      "cursor",
      RESIZE_CURSORS[edge],
      "important"
    );
    try {
      if (typeof node.setPointerCapture === "function") {
        node.setPointerCapture(event.pointerId);
      }
    } catch {
    }
  };
  const handlePointerDown = (event) => {
    onPointerDown?.(event);
    onActivate?.();
    if (dragDisabled || maximized || !onPositionChange || event.button !== 0) {
      return;
    }
    const target = event.target;
    if (!(target instanceof Element) || !rootRef.current) {
      return;
    }
    if (target.closest(".title-bar-controls") || target.closest(".window-resize-handle")) {
      return;
    }
    const titleBar = findShellTitleBar(rootRef.current);
    if (!titleBar || !titleBar.contains(target)) {
      return;
    }
    const preventNativeDrag = (dragEvent) => {
      dragEvent.preventDefault();
    };
    titleBar.addEventListener("dragstart", preventNativeDrag, { once: true });
    const rect = rootRef.current.getBoundingClientRect();
    dragRef.current = {
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      offsetX: event.clientX - rect.left,
      offsetY: event.clientY - rect.top,
      active: false
    };
  };
  const handleDoubleClick = (event) => {
    if (!resizable || !onToggleMaximize || !rootRef.current) {
      return;
    }
    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }
    if (target.closest(".title-bar-controls")) {
      return;
    }
    const titleBar = findShellTitleBar(rootRef.current);
    if (!titleBar || !titleBar.contains(target)) {
      return;
    }
    event.preventDefault();
    onToggleMaximize();
  };
  const sized = width !== void 0 && height !== void 0;
  const mergedStyle = {
    ...style,
    left,
    top,
    zIndex,
    ...sized ? { width, height } : null
  };
  const showHandles = resizable && !maximized && !dragDisabled;
  return /* @__PURE__ */ jsxs41(
    "div",
    {
      ref: rootRef,
      id: windowId,
      "data-shell-window": windowId,
      className: cn(
        "desktop-window",
        sized && "is-sized",
        maximized && "is-maximized",
        className
      ),
      style: mergedStyle,
      onPointerDown: handlePointerDown,
      onDoubleClick: handleDoubleClick,
      ...rest,
      children: [
        children,
        showHandles ? RESIZE_EDGES.map((edge) => /* @__PURE__ */ jsx64(
          "div",
          {
            className: "window-resize-handle",
            "data-edge": edge,
            "aria-hidden": true,
            onPointerDown: (event) => startResize(edge, event)
          },
          edge
        )) : null
      ]
    }
  );
}

// src/admin/shell/TaskbarClock.tsx
import { useEffect as useEffect13, useState as useState14 } from "react";
import { jsx as jsx65 } from "react/jsx-runtime";
function formatClock(date) {
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${hours}:${minutes}`;
}
function TaskbarClock() {
  const [label, setLabel] = useState14(() => formatClock(/* @__PURE__ */ new Date()));
  useEffect13(() => {
    const tick = () => setLabel(formatClock(/* @__PURE__ */ new Date()));
    tick();
    const id = window.setInterval(tick, 1e3);
    return () => window.clearInterval(id);
  }, []);
  return /* @__PURE__ */ jsx65("div", { className: "sunken-panel clock", "aria-live": "polite", children: label });
}

// src/admin/shell/Taskbar.tsx
import { jsx as jsx66, jsxs as jsxs42 } from "react/jsx-runtime";
function taskClassName(win, active) {
  return cn(
    "task",
    win.kind === "control-panel" && "control-panel",
    win.kind === "site" && "site",
    win.kind === "sites" && "sites",
    win.kind === "hosts" && "hosts",
    active && "active"
  );
}
function Taskbar({
  windows,
  activeId,
  onTaskClick,
  onMenuClick,
  menuExpanded = false,
  startMenu,
  className
}) {
  return /* @__PURE__ */ jsxs42("div", { id: "toolbar", className: cn("window", className), children: [
    startMenu,
    /* @__PURE__ */ jsxs42("div", { className: "window-body", children: [
      /* @__PURE__ */ jsx66(
        "button",
        {
          type: "button",
          className: "menu",
          "aria-expanded": menuExpanded,
          "aria-controls": "start-menu",
          "aria-haspopup": "menu",
          onClick: onMenuClick,
          children: "Menu"
        }
      ),
      /* @__PURE__ */ jsx66("div", { className: "task-buttons", children: windows.map((win) => {
        const pressed = win.id === activeId && !win.minimized;
        return /* @__PURE__ */ jsx66(
          "button",
          {
            type: "button",
            className: taskClassName(win, pressed),
            "data-window": win.id,
            "aria-pressed": pressed,
            title: win.title,
            onClick: () => onTaskClick(win.id),
            children: /* @__PURE__ */ jsx66("span", { className: "task-title", children: win.title })
          },
          win.id
        );
      }) }),
      /* @__PURE__ */ jsx66(TaskbarClock, {})
    ] })
  ] });
}

// src/admin/shell/StartMenu.tsx
import { useEffect as useEffect14, useRef as useRef10 } from "react";
import { jsx as jsx67, jsxs as jsxs43 } from "react/jsx-runtime";
function StartMenu({
  open,
  onClose,
  onOpenControlPanel,
  logoutHref
}) {
  const rootRef = useRef10(null);
  useEffect14(() => {
    if (!open) {
      return;
    }
    const onPointerDown = (event) => {
      const target = event.target;
      if (!(target instanceof Node)) {
        return;
      }
      if (rootRef.current?.contains(target)) {
        return;
      }
      if (target instanceof Element && target.closest("#toolbar > .window-body > button.menu")) {
        return;
      }
      onClose();
    };
    const onKeyDown = (event) => {
      if (event.key === "Escape") {
        onClose();
      }
    };
    window.addEventListener("pointerdown", onPointerDown, true);
    window.addEventListener("keydown", onKeyDown);
    return () => {
      window.removeEventListener("pointerdown", onPointerDown, true);
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [open, onClose]);
  const items = [
    { id: "uploads", label: "Uploads", className: "uploads", disabled: true },
    {
      id: "control-panel",
      label: "Control Panel",
      className: "control-panel",
      onSelect: () => {
        onOpenControlPanel();
        onClose();
      }
    },
    { id: "search", label: "Search", className: "search", disabled: true },
    { id: "logs", label: "Logs", className: "logs", disabled: true },
    { id: "about", label: "About", className: "about", disabled: true },
    {
      id: "logout",
      label: "Logout",
      className: "logout",
      disabled: !logoutHref,
      onSelect: logoutHref ? () => {
        onClose();
        window.location.assign(logoutHref);
      } : void 0
    }
  ];
  return /* @__PURE__ */ jsxs43(
    "div",
    {
      ref: rootRef,
      className: "start-menu",
      id: "start-menu",
      hidden: !open,
      children: [
        /* @__PURE__ */ jsx67("div", { className: "start-menu-banner", "aria-hidden": "true", children: /* @__PURE__ */ jsx67("span", { children: "WebHemi 1.0" }) }),
        /* @__PURE__ */ jsxs43("ul", { className: "start-menu-list", role: "menu", "aria-label": "Menu", children: [
          items.slice(0, 4).map((item) => /* @__PURE__ */ jsx67("li", { role: "none", children: /* @__PURE__ */ jsx67(StartMenuItem, { item }) }, item.id)),
          /* @__PURE__ */ jsx67("li", { className: "separator", role: "separator" }),
          items.slice(4).map((item) => /* @__PURE__ */ jsx67("li", { role: "none", children: /* @__PURE__ */ jsx67(StartMenuItem, { item }) }, item.id))
        ] })
      ]
    }
  );
}
function StartMenuItem({ item }) {
  return /* @__PURE__ */ jsx67(
    "button",
    {
      type: "button",
      role: "menuitem",
      className: cn("start-item", item.className),
      disabled: item.disabled,
      onClick: () => {
        if (!item.disabled) {
          item.onSelect?.();
        }
      },
      children: item.label
    }
  );
}

// src/admin/shell/persistence.ts
var DESKTOP_WINDOWS_STORAGE_KEY = "webhemi.admin.desktop.windows.v1";
function isFiniteNumber(value) {
  return typeof value === "number" && Number.isFinite(value);
}
function parseRestore(value) {
  if (!value || typeof value !== "object") {
    return void 0;
  }
  const restore = value;
  if (!isFiniteNumber(restore.left) || !isFiniteNumber(restore.top) || !isFiniteNumber(restore.width) || !isFiniteNumber(restore.height)) {
    return void 0;
  }
  return {
    left: restore.left,
    top: restore.top,
    width: restore.width,
    height: restore.height
  };
}
function parseEntry(id, value) {
  if (!value || typeof value !== "object") {
    return null;
  }
  const raw = value;
  const kind = raw.kind === "site" || raw.kind === "control-panel" || raw.kind === "sites" || raw.kind === "hosts" ? raw.kind : null;
  if (!kind) {
    return null;
  }
  if (!isFiniteNumber(raw.left) || !isFiniteNumber(raw.top) || !isFiniteNumber(raw.z) || !isFiniteNumber(raw.width) || !isFiniteNumber(raw.height)) {
    return null;
  }
  const siteId = kind === "site" ? parseSiteWindowId(id) : void 0;
  if (kind === "site" && siteId === null) {
    return null;
  }
  if (kind === "sites" && id !== SITES_WINDOW_ID) {
    return null;
  }
  if (kind === "hosts" && id !== HOSTS_WINDOW_ID) {
    return null;
  }
  if (kind === "control-panel" && id !== CONTROL_PANEL_WINDOW_ID) {
    return null;
  }
  return {
    id,
    kind,
    title: typeof raw.title === "string" ? raw.title : id,
    siteId: siteId ?? void 0,
    left: raw.left,
    top: raw.top,
    z: raw.z,
    width: raw.width,
    height: raw.height,
    minimized: Boolean(raw.minimized),
    maximized: Boolean(raw.maximized),
    restore: parseRestore(raw.restore),
    closed: Boolean(raw.closed)
  };
}
function loadPersistedDesktop(storageKey = DESKTOP_WINDOWS_STORAGE_KEY) {
  if (typeof localStorage === "undefined") {
    return null;
  }
  try {
    const raw = localStorage.getItem(storageKey);
    if (!raw) {
      return null;
    }
    const parsed = JSON.parse(raw);
    if (!parsed || typeof parsed !== "object") {
      return null;
    }
    const data = parsed;
    if (data.v !== 1 || !data.entries || typeof data.entries !== "object") {
      return null;
    }
    const entries = {};
    for (const [id, value] of Object.entries(
      data.entries
    )) {
      const entry = parseEntry(id, value);
      if (entry) {
        entries[id] = entry;
      }
    }
    return {
      v: 1,
      activeId: typeof data.activeId === "string" ? data.activeId : null,
      nextZ: isFiniteNumber(data.nextZ) ? data.nextZ : 10,
      cascade: isFiniteNumber(data.cascade) ? data.cascade : 0,
      entries
    };
  } catch {
    return null;
  }
}
function savePersistedDesktop(state, storageKey = DESKTOP_WINDOWS_STORAGE_KEY) {
  if (typeof localStorage === "undefined") {
    return;
  }
  try {
    localStorage.setItem(storageKey, JSON.stringify(state));
  } catch {
  }
}
function entryFromWindow(win, closed = false) {
  return {
    id: win.id,
    kind: win.kind,
    title: win.title,
    siteId: win.siteId,
    left: win.left,
    top: win.top,
    z: win.z,
    width: win.width,
    height: win.height,
    minimized: win.minimized,
    maximized: win.maximized,
    restore: win.restore,
    closed
  };
}
function windowFromEntry(entry) {
  return {
    id: entry.id,
    kind: entry.kind,
    title: entry.title,
    siteId: entry.siteId,
    left: entry.left,
    top: entry.top,
    z: entry.z,
    width: entry.width,
    height: entry.height,
    minimized: entry.minimized,
    maximized: entry.maximized,
    restore: entry.restore
  };
}
function hydrateDesktopFromPersistence(persisted, sites) {
  if (!persisted) {
    return { windows: [], activeId: null, nextZ: 10, cascade: 0 };
  }
  const siteIds = new Set(sites.map((site) => site.id));
  const siteName = new Map(sites.map((site) => [site.id, site.name]));
  const windows = [];
  for (const entry of Object.values(persisted.entries)) {
    if (entry.closed) {
      continue;
    }
    if (entry.kind === "control-panel" && entry.id === CONTROL_PANEL_WINDOW_ID) {
      windows.push(windowFromEntry(entry));
      continue;
    }
    if (entry.kind === "sites" && entry.id === SITES_WINDOW_ID) {
      windows.push(windowFromEntry(entry));
      continue;
    }
    if (entry.kind === "hosts" && entry.id === HOSTS_WINDOW_ID) {
      windows.push(windowFromEntry(entry));
      continue;
    }
    if (entry.kind === "site" && entry.siteId != null && siteIds.has(entry.siteId)) {
      windows.push({
        ...windowFromEntry(entry),
        title: siteName.get(entry.siteId) ?? entry.title
      });
    }
  }
  windows.sort((a, b) => a.z - b.z);
  const openIds = new Set(windows.map((win) => win.id));
  const activeId = persisted.activeId && openIds.has(persisted.activeId) ? persisted.activeId : windows.reduce(
    (best, win) => !best || win.z > best.z ? win : best,
    null
  )?.id ?? null;
  return {
    windows,
    activeId,
    nextZ: Math.max(persisted.nextZ, ...windows.map((win) => win.z), 10),
    cascade: persisted.cascade
  };
}
function buildPersistedDesktopState(previous, windows, activeId, nextZ, cascade) {
  const entries = {
    ...previous?.entries ?? {}
  };
  const openIds = new Set(windows.map((win) => win.id));
  for (const [id, entry] of Object.entries(entries)) {
    if (!openIds.has(id)) {
      entries[id] = { ...entry, closed: true };
    }
  }
  for (const win of windows) {
    entries[win.id] = entryFromWindow(win, false);
  }
  return {
    v: 1,
    activeId,
    nextZ,
    cascade,
    entries
  };
}
function geometryFromPersistence(persisted, id, kind) {
  const entry = persisted?.entries[id];
  if (!entry || entry.kind !== kind) {
    return null;
  }
  return {
    left: entry.left,
    top: entry.top,
    width: entry.width,
    height: entry.height,
    z: entry.z
  };
}

// src/admin/pages/AdminDesktop.tsx
import { jsx as jsx68, jsxs as jsxs44 } from "react/jsx-runtime";
var CASCADE_ORIGIN = { left: 48, top: 24 };
var CASCADE_STEP = 28;
var PERSIST_DEBOUNCE_MS = 200;
function topVisibleWindowId(windows) {
  return windows.filter((win) => !win.minimized).reduce(
    (best, win) => !best || win.z > best.z ? win : best,
    null
  )?.id ?? null;
}
function toDesktopSite(site) {
  return {
    id: site.id,
    name: site.name,
    slug: site.slug,
    enabled: site.enabled
  };
}
function toWindowSite(site) {
  return {
    id: site.id,
    name: site.name,
    slug: site.slug,
    enabled: site.enabled,
    hostCount: site.hostCount
  };
}
function toWindowHost(host) {
  return {
    id: host.id,
    host: host.host,
    siteId: host.siteId,
    siteSlug: host.siteSlug,
    siteName: host.siteName,
    surface: host.surface,
    verification: host.verification,
    enabled: host.enabled
  };
}
function toSiteFormHostOption(host) {
  return {
    id: host.id,
    host: host.host,
    siteId: host.siteId,
    siteName: host.siteName,
    status: host.verification === "verified" ? "verified" : "pending"
  };
}
function AdminDesktop({
  sites = [],
  explorerTreeForSite = buildEmptySiteExplorerTree,
  logoutHref,
  apiCsrfToken,
  errorSoundUrl,
  dingSoundUrl,
  apiBaseUrl,
  apiFetch,
  sitesApi,
  persistenceKey = DESKTOP_WINDOWS_STORAGE_KEY,
  className
}) {
  const storageKey = persistenceKey === false ? null : persistenceKey;
  const persistedRef = useRef11(
    storageKey ? loadPersistedDesktop(storageKey) : null
  );
  const hydratedRef = useRef11(
    hydrateDesktopFromPersistence(persistedRef.current, sites)
  );
  const nextZRef = useRef11(hydratedRef.current.nextZ);
  const cascadeRef = useRef11(hydratedRef.current.cascade);
  const dashboardRef = useRef11(null);
  const [shell, setShell] = useState15(() => ({
    windows: hydratedRef.current.windows,
    activeId: hydratedRef.current.activeId
  }));
  const [startMenuOpen, setStartMenuOpen] = useState15(false);
  const [desktopSites, setDesktopSites] = useState15(sites);
  const [sitesRows, setSitesRows] = useState15([]);
  const [sitesLoading, setSitesLoading] = useState15(false);
  const [sitesCreating, setSitesCreating] = useState15(false);
  const [sitesDeleting, setSitesDeleting] = useState15(false);
  const [sitesError, setSitesError] = useState15(null);
  const [sitesFormError, setSitesFormError] = useState15(null);
  const [sitesFieldErrors, setSitesFieldErrors] = useState15({});
  const [hostsRows, setHostsRows] = useState15([]);
  const [hostsLoading, setHostsLoading] = useState15(false);
  const [hostsCreating, setHostsCreating] = useState15(false);
  const [hostsDeleting, setHostsDeleting] = useState15(false);
  const [hostsUnassigning, setHostsUnassigning] = useState15(false);
  const [hostsAssigning, setHostsAssigning] = useState15(false);
  const [hostsVerifying, setHostsVerifying] = useState15(false);
  const [hostsError, setHostsError] = useState15(null);
  const [hostsFormError, setHostsFormError] = useState15(null);
  const [hostsFieldErrors, setHostsFieldErrors] = useState15({});
  const [sitesStatusMessage, setSitesStatusMessage] = useState15(null);
  const [hostsStatusMessage, setHostsStatusMessage] = useState15(null);
  const pendingLoginRedirectRef = useRef11(false);
  const sitesStatusTimerRef = useRef11(null);
  const hostsStatusTimerRef = useRef11(null);
  const api = useMemo5(
    () => sitesApi ?? createAdminApiClient({
      csrfToken: apiCsrfToken,
      baseUrl: apiBaseUrl,
      fetch: apiFetch
    }),
    [sitesApi, apiCsrfToken, apiBaseUrl, apiFetch]
  );
  const canEditSites = Boolean(sitesApi) || Boolean(apiCsrfToken);
  const canEditHosts = canEditSites;
  const clearSitesStatusMessage = useCallback5(() => {
    if (sitesStatusTimerRef.current != null) {
      clearTimeout(sitesStatusTimerRef.current);
      sitesStatusTimerRef.current = null;
    }
    setSitesStatusMessage(null);
  }, []);
  const clearHostsStatusMessage = useCallback5(() => {
    if (hostsStatusTimerRef.current != null) {
      clearTimeout(hostsStatusTimerRef.current);
      hostsStatusTimerRef.current = null;
    }
    setHostsStatusMessage(null);
  }, []);
  const flashSitesStatus = useCallback5(
    (message) => {
      clearSitesStatusMessage();
      setSitesStatusMessage(message);
      sitesStatusTimerRef.current = setTimeout(() => {
        sitesStatusTimerRef.current = null;
        setSitesStatusMessage(null);
      }, 4e3);
    },
    [clearSitesStatusMessage]
  );
  const flashHostsStatus = useCallback5(
    (message) => {
      clearHostsStatusMessage();
      setHostsStatusMessage(message);
      hostsStatusTimerRef.current = setTimeout(() => {
        hostsStatusTimerRef.current = null;
        setHostsStatusMessage(null);
      }, 4e3);
    },
    [clearHostsStatusMessage]
  );
  useEffect15(() => {
    return () => {
      if (sitesStatusTimerRef.current != null) {
        clearTimeout(sitesStatusTimerRef.current);
      }
      if (hostsStatusTimerRef.current != null) {
        clearTimeout(hostsStatusTimerRef.current);
      }
    };
  }, []);
  const noteUnauthorized = useCallback5((setError, message) => {
    pendingLoginRedirectRef.current = true;
    setError(message);
  }, []);
  const handleApiFailure = useCallback5(
    (result, setError) => {
      if (result.ok) {
        return;
      }
      if (isUnauthorizedResult(result)) {
        noteUnauthorized(setError, result.error.message);
        return;
      }
      pendingLoginRedirectRef.current = false;
      setError(result.error.message);
    },
    [noteUnauthorized]
  );
  const handleAlertClose = useCallback5(() => {
    if (pendingLoginRedirectRef.current) {
      window.location.assign("/login");
    }
  }, []);
  const sitesWindowOpen = shell.windows.some((win) => win.id === SITES_WINDOW_ID);
  const hostsWindowOpen = shell.windows.some((win) => win.id === HOSTS_WINDOW_ID);
  const siteFormHosts = useMemo5(
    () => hostsRows.map(toSiteFormHostOption),
    [hostsRows]
  );
  const hostFormSites = useMemo5(
    () => desktopSites.map((site) => ({
      id: site.id,
      name: site.name,
      slug: site.slug
    })),
    [desktopSites]
  );
  useEffect15(() => {
    setDesktopSites(sites);
  }, [sites]);
  useEffect15(() => {
    if (!storageKey) {
      return;
    }
    const timer = window.setTimeout(() => {
      const next = buildPersistedDesktopState(
        persistedRef.current,
        shell.windows,
        shell.activeId,
        nextZRef.current,
        cascadeRef.current
      );
      persistedRef.current = next;
      savePersistedDesktop(next, storageKey);
    }, PERSIST_DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [shell, storageKey]);
  useEffect15(() => {
    if (!sitesWindowOpen) {
      return;
    }
    let cancelled = false;
    setSitesLoading(true);
    setSitesError(null);
    clearSitesStatusMessage();
    void (async () => {
      const result = await api.listSites();
      if (cancelled) {
        return;
      }
      setSitesLoading(false);
      if (!result.ok) {
        handleApiFailure(result, setSitesError);
        return;
      }
      pendingLoginRedirectRef.current = false;
      setSitesRows(result.data.map(toWindowSite));
      setDesktopSites(result.data.map(toDesktopSite));
    })();
    return () => {
      cancelled = true;
    };
  }, [sitesWindowOpen, api, handleApiFailure, clearSitesStatusMessage]);
  useEffect15(() => {
    if (!hostsWindowOpen && !sitesWindowOpen) {
      return;
    }
    let cancelled = false;
    if (hostsWindowOpen) {
      setHostsLoading(true);
      setHostsError(null);
      clearHostsStatusMessage();
    }
    void (async () => {
      const result = await api.listHosts();
      if (cancelled) {
        return;
      }
      if (hostsWindowOpen) {
        setHostsLoading(false);
      }
      if (!result.ok) {
        if (hostsWindowOpen) {
          handleApiFailure(result, setHostsError);
        } else if (isUnauthorizedResult(result)) {
          noteUnauthorized(setSitesError, result.error.message);
        }
        return;
      }
      pendingLoginRedirectRef.current = false;
      setHostsRows(result.data.map(toWindowHost));
    })();
    return () => {
      cancelled = true;
    };
  }, [
    hostsWindowOpen,
    sitesWindowOpen,
    api,
    handleApiFailure,
    noteUnauthorized,
    clearHostsStatusMessage
  ]);
  const closeStartMenu = useCallback5(() => setStartMenuOpen(false), []);
  const toggleStartMenu = useCallback5(() => {
    setStartMenuOpen((open) => !open);
  }, []);
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
  const activateWindow = (id) => {
    setShell((prev) => {
      const target = prev.windows.find((win) => win.id === id);
      if (!target || target.minimized) {
        return prev;
      }
      if (target.z === nextZRef.current && prev.activeId === id) {
        return prev;
      }
      const z = target.z === nextZRef.current ? target.z : raiseZ();
      return {
        activeId: id,
        windows: prev.windows.map(
          (win) => win.id === id ? { ...win, z } : win
        )
      };
    });
  };
  const closeWindow = (id) => {
    setShell((prev) => {
      const windows = prev.windows.filter((win) => win.id !== id);
      return {
        windows,
        activeId: prev.activeId === id ? topVisibleWindowId(windows) : prev.activeId
      };
    });
  };
  const minimizeWindow = (id) => {
    setShell((prev) => {
      let windows = prev.windows.map(
        (win) => win.id === id ? { ...win, minimized: true } : win
      );
      let activeId = prev.activeId === id ? topVisibleWindowId(windows) : prev.activeId;
      if (activeId && activeId !== prev.activeId) {
        const z = raiseZ();
        windows = windows.map(
          (win) => win.id === activeId ? { ...win, z } : win
        );
      }
      return { windows, activeId };
    });
  };
  const restoreAndActivate = (id) => {
    setShell((prev) => {
      const target = prev.windows.find((win) => win.id === id);
      if (!target) {
        return prev;
      }
      const z = !target.minimized && target.z === nextZRef.current ? target.z : raiseZ();
      return {
        activeId: id,
        windows: prev.windows.map(
          (win) => win.id === id ? { ...win, minimized: false, z } : win
        )
      };
    });
  };
  const handleTaskClick = (id) => {
    const target = shell.windows.find((win) => win.id === id);
    if (!target) {
      return;
    }
    if (!target.minimized && shell.activeId === id) {
      minimizeWindow(id);
      return;
    }
    restoreAndActivate(id);
  };
  const toggleMaximize = (id) => {
    setShell((prev) => {
      const target = prev.windows.find((win) => win.id === id);
      if (!target) {
        return prev;
      }
      const z = target.z === nextZRef.current ? target.z : raiseZ();
      if (target.maximized && target.restore) {
        return {
          activeId: id,
          windows: prev.windows.map(
            (win) => win.id === id ? {
              ...win,
              z,
              maximized: false,
              left: target.restore.left,
              top: target.restore.top,
              width: target.restore.width,
              height: target.restore.height,
              restore: void 0
            } : win
          )
        };
      }
      const dashboard = dashboardRef.current;
      const work = dashboard ? getDesktopWorkSize(dashboard) : { width: target.width, height: target.height };
      return {
        activeId: id,
        windows: prev.windows.map(
          (win) => win.id === id ? {
            ...win,
            z,
            maximized: true,
            left: 0,
            top: 0,
            width: work.width,
            height: work.height,
            restore: {
              left: target.left,
              top: target.top,
              width: target.width,
              height: target.height
            }
          } : win
        )
      };
    });
  };
  const openOrRaiseWindow = (id, kind, title, defaultSize) => {
    setShell((prev) => {
      const existing = prev.windows.find((win) => win.id === id);
      if (existing) {
        const z = !existing.minimized && existing.z === nextZRef.current ? existing.z : raiseZ();
        return {
          activeId: id,
          windows: prev.windows.map(
            (win) => win.id === id ? { ...win, z, minimized: false } : win
          )
        };
      }
      const saved = geometryFromPersistence(persistedRef.current, id, kind);
      const place = saved ? { left: saved.left, top: saved.top, z: raiseZ() } : allocatePlacement();
      const size = saved ? { width: saved.width, height: saved.height } : defaultSize;
      return {
        activeId: id,
        windows: [
          ...prev.windows,
          {
            id,
            kind,
            title,
            left: place.left,
            top: place.top,
            z: place.z,
            width: size.width,
            height: size.height,
            minimized: false,
            maximized: false
          }
        ]
      };
    });
  };
  const openControlPanel = () => {
    openOrRaiseWindow(
      CONTROL_PANEL_WINDOW_ID,
      "control-panel",
      "Control Panel",
      DEFAULT_WINDOW_SIZE["control-panel"]
    );
  };
  const openSites = () => {
    openOrRaiseWindow(
      SITES_WINDOW_ID,
      "sites",
      "Sites",
      DEFAULT_WINDOW_SIZE.sites
    );
  };
  const openHosts = () => {
    openOrRaiseWindow(
      HOSTS_WINDOW_ID,
      "hosts",
      "Hosts",
      DEFAULT_WINDOW_SIZE.hosts
    );
  };
  const openSite = (site) => {
    const id = siteWindowId(site.id);
    setShell((prev) => {
      const existing = prev.windows.find((win) => win.id === id);
      if (existing) {
        const z = !existing.minimized && existing.z === nextZRef.current ? existing.z : raiseZ();
        return {
          activeId: id,
          windows: prev.windows.map(
            (win) => win.id === id ? { ...win, z, minimized: false } : win
          )
        };
      }
      const saved = geometryFromPersistence(persistedRef.current, id, "site");
      const place = saved ? { left: saved.left, top: saved.top, z: raiseZ() } : allocatePlacement();
      const size = saved ? { width: saved.width, height: saved.height } : DEFAULT_WINDOW_SIZE.site;
      return {
        activeId: id,
        windows: [
          ...prev.windows,
          {
            id,
            kind: "site",
            title: site.name,
            siteId: site.id,
            left: place.left,
            top: place.top,
            z: place.z,
            width: size.width,
            height: size.height,
            minimized: false,
            maximized: false
          }
        ]
      };
    });
  };
  const moveWindow = (id, left, top) => {
    setShell((prev) => ({
      ...prev,
      windows: prev.windows.map(
        (win) => win.id === id ? { ...win, left, top } : win
      )
    }));
  };
  const resizeWindow = (id, bounds) => {
    setShell((prev) => ({
      ...prev,
      windows: prev.windows.map(
        (win) => win.id === id ? {
          ...win,
          left: bounds.left,
          top: bounds.top,
          width: bounds.width,
          height: bounds.height,
          maximized: false,
          restore: void 0
        } : win
      )
    }));
  };
  const handleSaveSite = async (payload) => {
    clearSitesStatusMessage();
    setSitesCreating(true);
    setSitesFormError(null);
    setSitesFieldErrors({});
    const result = payload.mode === "new" ? await api.createSite({
      name: payload.name,
      slug: payload.slug,
      enabled: payload.enabled
    }) : payload.siteId != null ? await api.updateSite(payload.siteId, {
      name: payload.name,
      slug: payload.slug,
      enabled: payload.enabled
    }) : null;
    setSitesCreating(false);
    if (!result) {
      setSitesFormError("Site could not be saved.");
      return;
    }
    if (!result.ok) {
      if (result.error.fields) {
        setSitesFieldErrors({
          name: result.error.fields.name,
          slug: result.error.fields.slug
        });
      }
      handleApiFailure(result, setSitesFormError);
      return;
    }
    flashSitesStatus(payload.mode === "new" ? "Site created." : "Site updated.");
    const list = await api.listSites();
    if (list.ok) {
      setSitesRows(list.data.map(toWindowSite));
      setDesktopSites(list.data.map(toDesktopSite));
      return;
    }
    const saved = toWindowSite(result.data);
    setSitesRows(
      (prev) => [...prev.filter((row) => row.id !== saved.id), saved].sort(
        (a, b) => a.name.localeCompare(b.name)
      )
    );
    setDesktopSites((prev) => {
      const next = [
        ...prev.filter((row) => row.id !== saved.id),
        toDesktopSite(saved)
      ];
      return next.sort((a, b) => a.name.localeCompare(b.name));
    });
  };
  const handleDeleteSite = async (site) => {
    clearSitesStatusMessage();
    setSitesDeleting(true);
    setSitesError(null);
    const result = await api.deleteSite(site.id);
    setSitesDeleting(false);
    if (!result.ok) {
      handleApiFailure(result, setSitesError);
      return;
    }
    flashSitesStatus("Site deleted.");
    const list = await api.listSites();
    if (list.ok) {
      setSitesRows(list.data.map(toWindowSite));
      setDesktopSites(list.data.map(toDesktopSite));
    } else {
      setSitesRows((prev) => prev.filter((row) => row.id !== site.id));
      setDesktopSites((prev) => prev.filter((row) => row.id !== site.id));
    }
  };
  const handleSaveHost = async (payload) => {
    clearHostsStatusMessage();
    setHostsCreating(true);
    setHostsFormError(null);
    setHostsFieldErrors({});
    const result = payload.mode === "new" ? await api.createHost({
      host: payload.host,
      siteId: payload.siteId,
      surface: payload.surface,
      enabled: payload.enabled
    }) : payload.hostId != null ? await api.updateHost(payload.hostId, {
      host: payload.host,
      siteId: payload.siteId,
      surface: payload.surface,
      enabled: payload.enabled
    }) : null;
    setHostsCreating(false);
    if (!result) {
      setHostsFormError("Host could not be saved.");
      return;
    }
    if (!result.ok) {
      if (result.error.fields) {
        setHostsFieldErrors({
          host: result.error.fields.host,
          siteId: result.error.fields.siteId,
          surface: result.error.fields.surface,
          enabled: result.error.fields.enabled ?? result.error.fields.active
        });
      }
      handleApiFailure(result, setHostsFormError);
      return;
    }
    flashHostsStatus(payload.mode === "new" ? "Host created." : "Host updated.");
    const list = await api.listHosts();
    if (list.ok) {
      setHostsRows(list.data.map(toWindowHost));
    } else {
      const saved = toWindowHost(result.data);
      setHostsRows(
        (prev) => [...prev.filter((row) => row.id !== saved.id), saved].sort(
          (a, b) => a.host.localeCompare(b.host)
        )
      );
    }
    const sitesList = await api.listSites();
    if (sitesList.ok) {
      setSitesRows(sitesList.data.map(toWindowSite));
      setDesktopSites(sitesList.data.map(toDesktopSite));
    }
  };
  const handleDeleteHost = async (host) => {
    clearHostsStatusMessage();
    setHostsDeleting(true);
    setHostsError(null);
    const result = await api.deleteHost(host.id);
    setHostsDeleting(false);
    if (!result.ok) {
      handleApiFailure(result, setHostsError);
      return;
    }
    flashHostsStatus("Host deleted.");
    const list = await api.listHosts();
    if (list.ok) {
      setHostsRows(list.data.map(toWindowHost));
    } else {
      setHostsRows((prev) => prev.filter((row) => row.id !== host.id));
    }
    const sitesList = await api.listSites();
    if (sitesList.ok) {
      setSitesRows(sitesList.data.map(toWindowSite));
      setDesktopSites(sitesList.data.map(toDesktopSite));
    } else if (host.siteId != null) {
      setSitesRows(
        (prev) => prev.map(
          (row) => row.id === host.siteId ? { ...row, hostCount: Math.max(0, row.hostCount - 1) } : row
        )
      );
    }
  };
  const handleUnassignHost = async (hostId) => {
    clearSitesStatusMessage();
    setHostsUnassigning(true);
    setSitesFormError(null);
    const result = await api.unassignHost(hostId);
    setHostsUnassigning(false);
    if (!result.ok) {
      handleApiFailure(result, setSitesFormError);
      return;
    }
    flashSitesStatus("Host removed from site.");
    const list = await api.listHosts();
    if (list.ok) {
      setHostsRows(list.data.map(toWindowHost));
    } else {
      setHostsRows(
        (prev) => prev.map(
          (row) => row.id === hostId ? {
            ...row,
            siteId: null,
            siteSlug: null,
            siteName: null,
            status: row.verification
          } : row
        )
      );
    }
    const sitesList = await api.listSites();
    if (sitesList.ok) {
      setSitesRows(sitesList.data.map(toWindowSite));
      setDesktopSites(sitesList.data.map(toDesktopSite));
    }
  };
  const handleAssignHost = async (hostId, siteId) => {
    clearSitesStatusMessage();
    setHostsAssigning(true);
    setSitesFormError(null);
    const result = await api.assignHost(hostId, { siteId });
    setHostsAssigning(false);
    if (!result.ok) {
      handleApiFailure(result, setSitesFormError);
      return;
    }
    flashSitesStatus("Host assigned.");
    const list = await api.listHosts();
    if (list.ok) {
      setHostsRows(list.data.map(toWindowHost));
    } else {
      const assigned = toWindowHost(result.data);
      setHostsRows(
        (prev) => prev.map((row) => row.id === assigned.id ? assigned : row)
      );
    }
    const sitesList = await api.listSites();
    if (sitesList.ok) {
      setSitesRows(sitesList.data.map(toWindowSite));
      setDesktopSites(sitesList.data.map(toDesktopSite));
    }
  };
  const handleVerifyHost = async (host) => {
    clearHostsStatusMessage();
    setHostsVerifying(true);
    setHostsError(null);
    const result = await api.verifyHost(host.id);
    setHostsVerifying(false);
    if (!result.ok) {
      handleApiFailure(result, setHostsError);
      return;
    }
    flashHostsStatus("Host verified.");
    const list = await api.listHosts();
    if (list.ok) {
      setHostsRows(list.data.map(toWindowHost));
    } else {
      const verified = toWindowHost(result.data);
      setHostsRows(
        (prev) => prev.map((row) => row.id === verified.id ? verified : row)
      );
    }
  };
  return /* @__PURE__ */ jsxs44("div", { ref: dashboardRef, className: cn("dashboard", className), children: [
    /* @__PURE__ */ jsxs44("div", { className: "icon-list", children: [
      desktopSites.map((site) => /* @__PURE__ */ jsx68(
        SystemIcon,
        {
          kind: "site",
          label: site.name,
          labelTone: "light",
          onOpen: () => openSite(site)
        },
        site.id
      )),
      /* @__PURE__ */ jsx68(
        SystemIcon,
        {
          kind: "control-panel",
          label: "Control Panel",
          labelTone: "light",
          onOpen: openControlPanel
        }
      )
    ] }),
    shell.windows.map((win) => {
      const active = win.id === shell.activeId && !win.minimized;
      const maximizeAction = win.maximized ? "Restore" : "Maximize";
      const shellFrame = (child) => /* @__PURE__ */ jsx68(
        DesktopWindow,
        {
          windowId: win.id,
          left: win.left,
          top: win.top,
          zIndex: win.z,
          width: win.width,
          height: win.height,
          maximized: win.maximized,
          className: cn(win.minimized && "is-minimized"),
          dragDisabled: win.minimized || win.maximized,
          onActivate: () => activateWindow(win.id),
          onPositionChange: (left, top) => moveWindow(win.id, left, top),
          onBoundsChange: (bounds) => resizeWindow(win.id, bounds),
          onToggleMaximize: () => toggleMaximize(win.id),
          children: child
        },
        win.id
      );
      if (win.kind === "control-panel") {
        return shellFrame(
          /* @__PURE__ */ jsx68(
            ControlPanel,
            {
              className: cn(win.maximized && "is-maximized"),
              inactive: !active,
              maximized: win.maximized,
              onClose: () => closeWindow(win.id),
              onMinimize: () => minimizeWindow(win.id),
              onMaximize: () => toggleMaximize(win.id),
              onActivate: () => activateWindow(win.id),
              onOpenSites: openSites,
              onOpenHosts: openHosts
            }
          )
        );
      }
      if (win.kind === "sites") {
        return shellFrame(
          /* @__PURE__ */ jsx68(
            SitesWindow,
            {
              className: cn(win.maximized && "is-maximized"),
              inactive: !active,
              maximized: win.maximized,
              sites: sitesRows,
              hosts: siteFormHosts,
              canEdit: canEditSites,
              loading: sitesLoading,
              saving: sitesCreating,
              deleting: sitesDeleting,
              error: sitesError,
              formError: sitesFormError,
              fieldErrors: sitesFieldErrors,
              statusMessage: sitesStatusMessage,
              onClearStatusMessage: clearSitesStatusMessage,
              onSave: handleSaveSite,
              onDelete: handleDeleteSite,
              onAddHost: openHosts,
              onAssignHost: handleAssignHost,
              onUnassignHost: handleUnassignHost,
              unassigning: hostsUnassigning,
              assigning: hostsAssigning,
              errorSoundUrl,
              dingSoundUrl,
              onAlertClose: handleAlertClose,
              onClose: () => closeWindow(win.id),
              onCancel: () => closeWindow(win.id),
              onMinimize: () => minimizeWindow(win.id),
              onMaximize: () => toggleMaximize(win.id),
              onActivate: () => activateWindow(win.id),
              width: win.width,
              style: { height: "100%", minHeight: 0, width: "100%" }
            }
          )
        );
      }
      if (win.kind === "hosts") {
        return shellFrame(
          /* @__PURE__ */ jsx68(
            HostsWindow,
            {
              className: cn(win.maximized && "is-maximized"),
              inactive: !active,
              maximized: win.maximized,
              hosts: hostsRows,
              sites: hostFormSites,
              canEdit: canEditHosts,
              loading: hostsLoading,
              saving: hostsCreating,
              deleting: hostsDeleting,
              verifying: hostsVerifying,
              error: hostsError,
              formError: hostsFormError,
              fieldErrors: hostsFieldErrors,
              statusMessage: hostsStatusMessage,
              onClearStatusMessage: clearHostsStatusMessage,
              onClearFormError: () => {
                setHostsFormError(null);
                setHostsFieldErrors({});
              },
              onSave: handleSaveHost,
              onDelete: handleDeleteHost,
              onVerify: handleVerifyHost,
              errorSoundUrl,
              dingSoundUrl,
              onAlertClose: handleAlertClose,
              onClose: () => closeWindow(win.id),
              onCancel: () => closeWindow(win.id),
              onMinimize: () => minimizeWindow(win.id),
              onMaximize: () => toggleMaximize(win.id),
              onActivate: () => activateWindow(win.id),
              width: win.width,
              style: { height: "100%", minHeight: 0, width: "100%" }
            }
          )
        );
      }
      const site = desktopSites.find((entry) => entry.id === win.siteId) ?? {
        id: win.siteId ?? 0,
        name: win.title
      };
      return shellFrame(
        /* @__PURE__ */ jsx68(
          SiteFileExplorer,
          {
            className: cn(win.maximized && "is-maximized"),
            inactive: !active,
            title: win.title,
            titleIcon: "site",
            tree: explorerTreeForSite(site),
            onClose: () => closeWindow(win.id),
            onMinimize: () => minimizeWindow(win.id),
            onMaximize: () => toggleMaximize(win.id),
            maximizeAction
          }
        )
      );
    }),
    /* @__PURE__ */ jsx68(
      Taskbar,
      {
        windows: shell.windows,
        activeId: shell.activeId,
        onTaskClick: handleTaskClick,
        onMenuClick: toggleStartMenu,
        menuExpanded: startMenuOpen,
        startMenu: /* @__PURE__ */ jsx68(
          StartMenu,
          {
            open: startMenuOpen,
            onClose: closeStartMenu,
            onOpenControlPanel: openControlPanel,
            logoutHref
          }
        )
      }
    )
  ] });
}

// src/admin/pages/AdminDashboard.tsx
import { jsx as jsx69, jsxs as jsxs45 } from "react/jsx-runtime";
function AdminDashboard({
  userLabel,
  navItems,
  siteCount = 0,
  hostCount = 0,
  flashes
}) {
  return /* @__PURE__ */ jsxs45(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Dashboard", children: [
    /* @__PURE__ */ jsx69(FlashList, { flashes }),
    /* @__PURE__ */ jsx69(
      PageHeader,
      {
        title: "Dashboard",
        description: "Multi-tenant control panel powered by @webhemi/ui."
      }
    ),
    /* @__PURE__ */ jsxs45("div", { style: { display: "flex", gap: "1.5rem", flexWrap: "wrap" }, children: [
      /* @__PURE__ */ jsxs45(Alert, { tone: "info", title: "Sites", children: [
        siteCount,
        " configured"
      ] }),
      /* @__PURE__ */ jsxs45(Alert, { tone: "info", title: "Hosts", children: [
        hostCount,
        " configured"
      ] })
    ] })
  ] });
}

// src/admin/pages/SitesPage.tsx
import { jsx as jsx70, jsxs as jsxs46 } from "react/jsx-runtime";
function SitesPage({
  userLabel,
  navItems,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs46(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Sites", children: [
    /* @__PURE__ */ jsx70(FlashList, { flashes }),
    /* @__PURE__ */ jsx70(SiteListView, { sites: sites || [], createHref: "#create-site" }),
    canEdit ? /* @__PURE__ */ jsxs46(
      "form",
      {
        id: "create-site",
        action: createAction,
        method: "post",
        noValidate: true,
        style: { marginTop: "2rem" },
        children: [
          /* @__PURE__ */ jsx70(FormField, { label: "Name", htmlFor: "name", required: true, children: /* @__PURE__ */ jsx70(Input, { id: "name", name: "name" }) }),
          /* @__PURE__ */ jsx70(FormField, { label: "Slug", htmlFor: "slug", required: true, hint: "Lowercase identifier", children: /* @__PURE__ */ jsx70(Input, { id: "slug", name: "slug" }) }),
          /* @__PURE__ */ jsx70(Button, { type: "submit", children: "Create site" })
        ]
      }
    ) : null
  ] });
}

// src/admin/pages/HostsPage.tsx
import { jsx as jsx71, jsxs as jsxs47 } from "react/jsx-runtime";
function HostsPage({
  userLabel,
  navItems,
  hosts,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs47(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Hosts", children: [
    /* @__PURE__ */ jsx71(FlashList, { flashes }),
    /* @__PURE__ */ jsx71(SiteHostListView, { hosts: hosts || [], createHref: "#create-host" }),
    canEdit ? /* @__PURE__ */ jsxs47(
      "form",
      {
        id: "create-host",
        action: createAction,
        method: "post",
        noValidate: true,
        style: { marginTop: "2rem" },
        children: [
          /* @__PURE__ */ jsx71(FormField, { label: "Hostname", htmlFor: "host", required: true, children: /* @__PURE__ */ jsx71(Input, { id: "host", name: "host", placeholder: "www.example.com" }) }),
          /* @__PURE__ */ jsx71(FormField, { label: "Site", htmlFor: "site_id", required: true, children: /* @__PURE__ */ jsx71(Select, { id: "site_id", name: "site_id", children: (sites || []).map((site) => /* @__PURE__ */ jsx71("option", { value: site.id, children: site.name }, site.id)) }) }),
          /* @__PURE__ */ jsx71(FormField, { label: "Surface", htmlFor: "surface", children: /* @__PURE__ */ jsxs47(Select, { id: "surface", name: "surface", defaultValue: "site", children: [
            /* @__PURE__ */ jsx71("option", { value: "admin", children: "admin" }),
            /* @__PURE__ */ jsx71("option", { value: "site", children: "site" }),
            /* @__PURE__ */ jsx71("option", { value: "api", children: "api" })
          ] }) }),
          /* @__PURE__ */ jsx71(Button, { type: "submit", children: "Add host" })
        ]
      }
    ) : null
  ] });
}

// src/themes/default/components/SiteHeader/SiteHeader.tsx
import { jsx as jsx72, jsxs as jsxs48 } from "react/jsx-runtime";
function SiteHeader({ siteName, navItems = [], actions, className }) {
  return /* @__PURE__ */ jsx72(
    "header",
    {
      className: cn(
        "wh-ui border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs48("div", { className: "mx-auto flex max-w-5xl items-center justify-between gap-6 px-6 py-4", children: [
        /* @__PURE__ */ jsx72(
          "a",
          {
            href: "/",
            className: "font-[family-name:var(--wh-font-display)] text-2xl text-[var(--wh-color-ink)] no-underline",
            children: siteName
          }
        ),
        /* @__PURE__ */ jsx72("nav", { className: "flex flex-1 items-center gap-4", "aria-label": "Primary", children: navItems.map((item) => /* @__PURE__ */ jsx72(
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
        actions ? /* @__PURE__ */ jsx72("div", { className: "flex items-center gap-2", children: actions }) : null
      ] })
    }
  );
}

// src/themes/default/components/Hero/Hero.tsx
import { jsx as jsx73, jsxs as jsxs49 } from "react/jsx-runtime";
function Hero({ title, subtitle, actions, className }) {
  return /* @__PURE__ */ jsxs49(
    "section",
    {
      className: cn(
        "wh-ui relative overflow-hidden bg-[var(--wh-color-ink)] text-[var(--wh-color-surface)]",
        className
      ),
      children: [
        /* @__PURE__ */ jsx73(
          "div",
          {
            className: "pointer-events-none absolute inset-0 opacity-40",
            style: {
              background: "radial-gradient(ellipse at 20% 20%, var(--wh-color-accent) 0%, transparent 55%), radial-gradient(ellipse at 80% 80%, var(--wh-color-accent-hot) 0%, transparent 50%)"
            },
            "aria-hidden": true
          }
        ),
        /* @__PURE__ */ jsxs49("div", { className: "relative mx-auto flex min-h-[70vh] max-w-5xl flex-col justify-end gap-4 px-6 pb-16 pt-24", children: [
          /* @__PURE__ */ jsx73("h1", { className: "max-w-3xl font-[family-name:var(--wh-font-display)] text-5xl leading-tight md:text-6xl", children: title }),
          subtitle ? /* @__PURE__ */ jsx73("p", { className: "max-w-xl text-lg text-[var(--wh-color-canvas)]/90", children: subtitle }) : null,
          actions ? /* @__PURE__ */ jsx73("div", { className: "mt-2 flex flex-wrap gap-3", children: actions }) : null
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
  DesktopModal,
  DialogWindow,
  EXPLORER_FIXTURE_ITEMS,
  EXPLORER_FIXTURE_SITE,
  EXPLORER_FIXTURE_TREE,
  ExplorerContent,
  ExplorerMenuBar,
  ExplorerPropertiesDialog,
  ExplorerSplitter,
  ExplorerToolbar,
  FieldBorder,
  FieldRow,
  FileExplorerWindow,
  FlashList,
  FloatingModal,
  FormField,
  GroupBox,
  HeadingPanelWindow,
  Hero,
  HostFormDialog,
  HostsPage,
  HostsWindow,
  Icon,
  IconPanelSelectionInfo,
  IconPanelWindow,
  Input,
  Label,
  LoginForm,
  LoginPage,
  MessageDialog,
  Modal,
  OWNED_MODAL_FLASH_COUNT,
  OWNED_MODAL_FLASH_INTERVAL_MS,
  PageHeader,
  Pagination,
  PaneWindowShell,
  Progress,
  Radio,
  RoleListView,
  SESSION_EXPIRED_MESSAGE,
  Scrollable,
  Select2 as Select,
  Sidebar,
  SiteFileExplorer,
  SiteFormDialog,
  SiteHeader,
  SiteHostListView,
  SiteListView,
  SitesPage,
  SitesWindow,
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
  TreeToggle,
  TreeView,
  UserListView,
  VerticalBar,
  Window,
  WindowBody,
  WizardWindow,
  adminAsset,
  adminIconAsset,
  attachCustomScrollbar,
  buildDemoSiteExplorerTree,
  buildEmptySiteExplorerTree,
  canCutOrCopyExplorerItem,
  canCutOrCopyExplorerItems,
  canDeleteExplorerItem,
  canPasteIntoExplorerLocation,
  cloneExplorerForest,
  createAdminApiClient,
  deleteExplorerItem,
  deleteExplorerItems,
  explorerContentItems,
  explorerTreeChildren,
  findExplorerAncestorIds,
  findExplorerItem,
  findExplorerParent,
  findExplorerTrashRoot,
  findTopFloatingModal,
  findTopOwnedFloatingModal,
  flashOwnedModalAttention,
  formatExplorerSize,
  isExplorerDocument,
  isExplorerFolder,
  isExplorerLocation,
  isExplorerTreeExpandable,
  isUnauthorizedResult,
  isUnderExplorerTrash,
  pasteExplorerClipboard,
  playAdminSound,
  promoteTabRow,
  resolveTitleBarIcon,
  undoExplorerAction,
  undoExplorerDelete,
  undoExplorerPaste,
  useCustomScrollbar,
  useTableView
};
//# sourceMappingURL=index.js.map