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

// src/shared/components/Checkbox/Checkbox.tsx
import { jsx as jsx5, jsxs as jsxs3 } from "react/jsx-runtime";
function Checkbox({ label, className, id, ...rest }) {
  const inputId = id ?? `wh-cb-${label.replace(/\s+/g, "-").toLowerCase()}`;
  return /* @__PURE__ */ jsxs3(
    "label",
    {
      htmlFor: inputId,
      className: cn(
        "wh-ui inline-flex cursor-pointer items-center gap-2 text-sm text-[var(--wh-color-ink)]",
        className
      ),
      children: [
        /* @__PURE__ */ jsx5(
          "input",
          {
            id: inputId,
            type: "checkbox",
            className: "size-4 accent-[var(--wh-color-accent)]",
            ...rest
          }
        ),
        /* @__PURE__ */ jsx5("span", { children: label })
      ]
    }
  );
}

// src/shared/components/Badge/Badge.tsx
import { jsx as jsx6 } from "react/jsx-runtime";
var toneClass = {
  neutral: "bg-[var(--wh-color-line)] text-[var(--wh-color-ink)]",
  success: "bg-[color-mix(in_srgb,var(--wh-color-success)_18%,white)] text-[var(--wh-color-success)]",
  warning: "bg-[color-mix(in_srgb,var(--wh-color-warning)_18%,white)] text-[var(--wh-color-warning)]",
  danger: "bg-[color-mix(in_srgb,var(--wh-color-danger)_18%,white)] text-[var(--wh-color-danger)]",
  accent: "bg-[color-mix(in_srgb,var(--wh-color-accent)_18%,white)] text-[var(--wh-color-accent)]"
};
function Badge({ tone = "neutral", className, children, ...rest }) {
  return /* @__PURE__ */ jsx6(
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
import { jsx as jsx7, jsxs as jsxs4 } from "react/jsx-runtime";
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
  return /* @__PURE__ */ jsxs4(
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
        title ? /* @__PURE__ */ jsx7("title", { children: title }) : null,
        /* @__PURE__ */ jsx7("path", { d: paths[name] })
      ]
    }
  );
}

// src/shared/components/FormField/FormField.tsx
import { jsx as jsx8, jsxs as jsxs5 } from "react/jsx-runtime";
function FormField({
  label,
  htmlFor,
  required,
  error,
  hint,
  children,
  className
}) {
  return /* @__PURE__ */ jsxs5("div", { className: cn("wh-ui mb-4", className), children: [
    /* @__PURE__ */ jsx8(Label, { htmlFor, required, children: label }),
    children,
    hint && !error ? /* @__PURE__ */ jsx8("p", { className: "mt-1 text-sm text-[var(--wh-color-muted)]", children: hint }) : null,
    error ? /* @__PURE__ */ jsx8("p", { className: "mt-1 text-sm text-[var(--wh-color-danger)]", role: "alert", children: error }) : null
  ] });
}

// src/shared/components/Alert/Alert.tsx
import { jsx as jsx9, jsxs as jsxs6 } from "react/jsx-runtime";
var toneClass2 = {
  info: "border-[var(--wh-color-accent)] bg-[color-mix(in_srgb,var(--wh-color-accent)_10%,white)]",
  success: "border-[var(--wh-color-success)] bg-[color-mix(in_srgb,var(--wh-color-success)_10%,white)]",
  warning: "border-[var(--wh-color-warning)] bg-[color-mix(in_srgb,var(--wh-color-warning)_10%,white)]",
  danger: "border-[var(--wh-color-danger)] bg-[color-mix(in_srgb,var(--wh-color-danger)_10%,white)]"
};
function Alert({ tone = "info", title, children, className, ...rest }) {
  return /* @__PURE__ */ jsxs6(
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
        /* @__PURE__ */ jsx9(Icon, { name: tone === "success" ? "check" : "alert", className: "mt-0.5 text-lg" }),
        /* @__PURE__ */ jsxs6("div", { children: [
          title ? /* @__PURE__ */ jsx9("p", { className: "font-semibold", children: title }) : null,
          /* @__PURE__ */ jsx9("div", { className: "text-sm text-[var(--wh-color-ink-soft)]", children })
        ] })
      ]
    }
  );
}

// src/admin/components/FlashList/FlashList.tsx
import { jsx as jsx10 } from "react/jsx-runtime";
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
    ([tone, messages]) => messages.map((message, index) => /* @__PURE__ */ jsx10(Alert, { tone: toneForFlash(tone), className: "mb-4", children: message }, `${tone}-${index}`))
  );
}

// src/admin/components/DataTable/DataTable.tsx
import { jsx as jsx11, jsxs as jsxs7 } from "react/jsx-runtime";
function DataTable({
  columns,
  rows,
  rowKey,
  emptyMessage = "No records found.",
  loading = false,
  className
}) {
  if (loading) {
    return /* @__PURE__ */ jsx11("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: "Loading\u2026" });
  }
  if (rows.length === 0) {
    return /* @__PURE__ */ jsx11("div", { className: "wh-ui rounded-[var(--wh-radius-md)] border border-dashed border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 text-center text-[var(--wh-color-muted)]", children: emptyMessage });
  }
  return /* @__PURE__ */ jsx11(
    "div",
    {
      className: cn(
        "wh-ui overflow-x-auto rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs7("table", { className: "w-full border-collapse text-left text-sm", children: [
        /* @__PURE__ */ jsx11("thead", { className: "bg-[var(--wh-color-canvas)] text-[var(--wh-color-muted)]", children: /* @__PURE__ */ jsx11("tr", { children: columns.map((col) => /* @__PURE__ */ jsx11("th", { className: cn("px-4 py-3 font-semibold", col.className), children: col.header }, col.key)) }) }),
        /* @__PURE__ */ jsx11("tbody", { children: rows.map((row) => /* @__PURE__ */ jsx11(
          "tr",
          {
            className: "border-t border-[var(--wh-color-line)] hover:bg-[color-mix(in_srgb,var(--wh-color-accent)_6%,white)]",
            children: columns.map((col) => /* @__PURE__ */ jsx11("td", { className: cn("px-4 py-3", col.className), children: col.render(row) }, col.key))
          },
          rowKey(row)
        )) })
      ] })
    }
  );
}

// src/admin/components/Pagination/Pagination.tsx
import { jsx as jsx12, jsxs as jsxs8 } from "react/jsx-runtime";
function Pagination({ page, pageCount, onPageChange, className }) {
  if (pageCount <= 1) {
    return null;
  }
  return /* @__PURE__ */ jsxs8(
    "nav",
    {
      className: cn("wh-ui mt-4 flex items-center justify-between gap-3", className),
      "aria-label": "Pagination",
      children: [
        /* @__PURE__ */ jsx12(
          Button,
          {
            variant: "secondary",
            size: "sm",
            disabled: page <= 1,
            onClick: () => onPageChange(page - 1),
            children: "Previous"
          }
        ),
        /* @__PURE__ */ jsxs8("span", { className: "text-sm text-[var(--wh-color-muted)]", children: [
          "Page ",
          page,
          " of ",
          pageCount
        ] }),
        /* @__PURE__ */ jsx12(
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
import { useEffect } from "react";
import { jsx as jsx13, jsxs as jsxs9 } from "react/jsx-runtime";
function Modal({ open, title, children, onClose, footer, className }) {
  useEffect(() => {
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
  return /* @__PURE__ */ jsxs9("div", { className: "wh-ui fixed inset-0 z-50 flex items-center justify-center p-4", children: [
    /* @__PURE__ */ jsx13(
      "button",
      {
        type: "button",
        className: "absolute inset-0 bg-[var(--wh-color-ink)]/50",
        "aria-label": "Close dialog",
        onClick: onClose
      }
    ),
    /* @__PURE__ */ jsxs9(
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
          /* @__PURE__ */ jsxs9("div", { className: "flex items-center justify-between border-b border-[var(--wh-color-line)] px-4 py-3", children: [
            /* @__PURE__ */ jsx13("h2", { id: "wh-modal-title", className: "font-[family-name:var(--wh-font-display)] text-lg", children: title }),
            /* @__PURE__ */ jsx13(Button, { variant: "ghost", size: "sm", onClick: onClose, "aria-label": "Close", children: "\xD7" })
          ] }),
          /* @__PURE__ */ jsx13("div", { className: "px-4 py-4", children }),
          footer ? /* @__PURE__ */ jsx13("div", { className: "flex justify-end gap-2 border-t border-[var(--wh-color-line)] px-4 py-3", children: footer }) : null
        ]
      }
    )
  ] });
}

// src/admin/components/PageHeader/PageHeader.tsx
import { jsx as jsx14, jsxs as jsxs10 } from "react/jsx-runtime";
function PageHeader({ title, description, actions, className }) {
  return /* @__PURE__ */ jsxs10(
    "header",
    {
      className: cn(
        "wh-ui mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-[var(--wh-color-line)] pb-4",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs10("div", { children: [
          /* @__PURE__ */ jsx14("h1", { className: "font-[family-name:var(--wh-font-display)] text-3xl tracking-tight text-[var(--wh-color-ink)]", children: title }),
          description ? /* @__PURE__ */ jsx14("p", { className: "mt-1 max-w-2xl text-[var(--wh-color-muted)]", children: description }) : null
        ] }),
        actions ? /* @__PURE__ */ jsx14("div", { className: "flex flex-wrap gap-2", children: actions }) : null
      ]
    }
  );
}

// src/admin/components/Sidebar/Sidebar.tsx
import { jsx as jsx15, jsxs as jsxs11 } from "react/jsx-runtime";
function Sidebar({ brand = "WebHemi", items, className }) {
  return /* @__PURE__ */ jsxs11(
    "aside",
    {
      className: cn(
        "wh-ui flex min-h-full w-60 flex-col border-r border-[var(--wh-color-line)] bg-[var(--wh-color-ink)] text-white",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs11("div", { className: "border-b border-white/10 px-5 py-5", children: [
          /* @__PURE__ */ jsx15("p", { className: "font-[family-name:var(--wh-font-display)] text-2xl tracking-tight", children: brand }),
          /* @__PURE__ */ jsx15("p", { className: "text-xs uppercase tracking-[0.2em] text-white/60", children: "Admin" })
        ] }),
        /* @__PURE__ */ jsx15("nav", { className: "flex flex-1 flex-col gap-1 p-3", "aria-label": "Admin", children: items.map((item) => /* @__PURE__ */ jsxs11(
          "a",
          {
            href: item.href,
            className: cn(
              "flex items-center gap-3 rounded-[var(--wh-radius-sm)] px-3 py-2 text-sm transition",
              item.active ? "bg-[var(--wh-color-accent)] text-white" : "text-white/80 hover:bg-white/10 hover:text-white"
            ),
            "aria-current": item.active ? "page" : void 0,
            children: [
              item.icon ? /* @__PURE__ */ jsx15(Icon, { name: item.icon }) : null,
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
import { jsx as jsx16, jsxs as jsxs12 } from "react/jsx-runtime";
function TopBar({ title, userLabel, actions, className }) {
  return /* @__PURE__ */ jsxs12(
    "div",
    {
      className: cn(
        "wh-ui flex items-center justify-between gap-4 border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] px-6 py-3",
        className
      ),
      children: [
        /* @__PURE__ */ jsx16("p", { className: "text-sm font-medium text-[var(--wh-color-muted)]", children: title ?? "Control panel" }),
        /* @__PURE__ */ jsxs12("div", { className: "flex items-center gap-3", children: [
          actions,
          userLabel ? /* @__PURE__ */ jsx16("span", { className: "rounded-[var(--wh-radius-sm)] bg-[var(--wh-color-canvas)] px-3 py-1 text-sm", children: userLabel }) : null
        ] })
      ]
    }
  );
}

// src/admin/components/AdminLayout/AdminLayout.tsx
import { jsx as jsx17, jsxs as jsxs13 } from "react/jsx-runtime";
function AdminLayout({
  brand,
  navItems,
  userLabel,
  topBarTitle,
  topBarActions,
  children,
  className
}) {
  return /* @__PURE__ */ jsxs13("div", { className: cn("wh-ui flex min-h-screen bg-[var(--wh-color-canvas)]", className), children: [
    /* @__PURE__ */ jsx17(Sidebar, { brand, items: navItems }),
    /* @__PURE__ */ jsxs13("div", { className: "flex min-w-0 flex-1 flex-col", children: [
      /* @__PURE__ */ jsx17(TopBar, { title: topBarTitle, userLabel, actions: topBarActions }),
      /* @__PURE__ */ jsx17("main", { className: "flex-1 p-6", children })
    ] })
  ] });
}

// src/admin/components/LoginForm/LoginForm.tsx
import { jsx as jsx18, jsxs as jsxs14 } from "react/jsx-runtime";
function LoginForm({
  action = "/login",
  method = "post",
  csrfToken,
  csrfFieldName = "_csrf_token",
  error,
  loading = false,
  emailDefault = "",
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
  return /* @__PURE__ */ jsxs14(
    "div",
    {
      className: cn(
        "wh-ui mx-auto w-full max-w-md rounded-[var(--wh-radius-md)] border border-[var(--wh-color-line)] bg-[var(--wh-color-surface)] p-8 shadow-sm",
        className
      ),
      children: [
        /* @__PURE__ */ jsxs14("div", { className: "mb-6 text-center", children: [
          /* @__PURE__ */ jsx18("p", { className: "font-[family-name:var(--wh-font-display)] text-3xl text-[var(--wh-color-ink)]", children: "WebHemi" }),
          /* @__PURE__ */ jsx18("p", { className: "mt-1 text-sm text-[var(--wh-color-muted)]", children: "Sign in to the control panel" })
        ] }),
        error ? /* @__PURE__ */ jsx18(Alert, { tone: "danger", title: "Sign-in failed", className: "mb-4", children: error }) : null,
        /* @__PURE__ */ jsxs14("form", { action, method, onSubmit: handleSubmit, children: [
          csrfToken ? /* @__PURE__ */ jsx18("input", { type: "hidden", name: csrfFieldName, value: csrfToken }) : null,
          /* @__PURE__ */ jsx18(FormField, { label: "Email", htmlFor: "email", required: true, children: /* @__PURE__ */ jsx18(
            Input,
            {
              id: "email",
              name: "email",
              type: "email",
              autoComplete: "username",
              defaultValue: emailDefault,
              required: true
            }
          ) }),
          /* @__PURE__ */ jsx18(FormField, { label: "Password", htmlFor: "password", required: true, children: /* @__PURE__ */ jsx18(
            Input,
            {
              id: "password",
              name: "password",
              type: "password",
              autoComplete: "current-password",
              required: true
            }
          ) }),
          /* @__PURE__ */ jsx18("div", { className: "mb-6", children: /* @__PURE__ */ jsx18(Checkbox, { name: "remember", label: "Remember me" }) }),
          /* @__PURE__ */ jsx18(Button, { type: "submit", className: "w-full", loading, children: "Sign in" })
        ] })
      ]
    }
  );
}

// src/admin/views/SiteListView.tsx
import { jsx as jsx19, jsxs as jsxs15 } from "react/jsx-runtime";
function SiteListView({
  sites,
  loading = false,
  createHref = "/admin/sites/new",
  editHref = (site) => `/admin/sites/${site.id}`
}) {
  return /* @__PURE__ */ jsxs15("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx19(
      PageHeader,
      {
        title: "Sites",
        description: "Multi-tenant sites bound to one or more hostnames.",
        actions: /* @__PURE__ */ jsx19("a", { href: createHref, children: /* @__PURE__ */ jsx19(Button, { children: "New site" }) })
      }
    ),
    /* @__PURE__ */ jsx19(
      DataTable,
      {
        loading,
        rows: sites,
        rowKey: (row) => row.id,
        emptyMessage: "No sites yet. Create the first tenant.",
        columns: [
          { key: "name", header: "Name", render: (row) => row.name },
          { key: "slug", header: "Slug", render: (row) => /* @__PURE__ */ jsx19("code", { children: row.slug }) },
          {
            key: "hosts",
            header: "Hosts",
            render: (row) => String(row.hostCount)
          },
          {
            key: "status",
            header: "Status",
            render: (row) => /* @__PURE__ */ jsx19(Badge, { tone: row.enabled ? "success" : "neutral", children: row.enabled ? "Enabled" : "Disabled" })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx19("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/SiteHostListView.tsx
import { jsx as jsx20, jsxs as jsxs16 } from "react/jsx-runtime";
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
  return /* @__PURE__ */ jsxs16("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx20(
      PageHeader,
      {
        title: "Hosts",
        description: "Domain names mapped to sites and surfaces (admin, site, api).",
        actions: /* @__PURE__ */ jsx20("a", { href: createHref, children: /* @__PURE__ */ jsx20(Button, { children: "Add host" }) })
      }
    ),
    /* @__PURE__ */ jsx20(
      DataTable,
      {
        loading,
        rows: hosts,
        rowKey: (row) => row.id,
        emptyMessage: "No hosts configured.",
        columns: [
          { key: "host", header: "Hostname", render: (row) => /* @__PURE__ */ jsx20("code", { children: row.host }) },
          { key: "site", header: "Site", render: (row) => row.siteName },
          {
            key: "surface",
            header: "Surface",
            render: (row) => /* @__PURE__ */ jsx20(Badge, { tone: "accent", children: row.surface })
          },
          {
            key: "status",
            header: "Status",
            render: (row) => /* @__PURE__ */ jsx20(Badge, { tone: statusTone[row.status], children: row.status })
          },
          {
            key: "actions",
            header: "",
            render: (row) => row.status === "pending" ? /* @__PURE__ */ jsx20("a", { href: verifyHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Verify" }) : "\u2014"
          }
        ]
      }
    )
  ] });
}

// src/admin/views/UserListView.tsx
import { jsx as jsx21, jsxs as jsxs17 } from "react/jsx-runtime";
function UserListView({
  users,
  loading = false,
  createHref = "/admin/users/new",
  editHref = (user) => `/admin/users/${user.id}`
}) {
  return /* @__PURE__ */ jsxs17("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx21(
      PageHeader,
      {
        title: "Users",
        description: "Accounts with global roles and optional per-site assignments.",
        actions: /* @__PURE__ */ jsx21("a", { href: createHref, children: /* @__PURE__ */ jsx21(Button, { children: "New user" }) })
      }
    ),
    /* @__PURE__ */ jsx21(
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
            render: (row) => /* @__PURE__ */ jsx21("div", { className: "flex flex-wrap gap-1", children: row.roles.map((role) => /* @__PURE__ */ jsx21(Badge, { children: role }, role)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx21("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/views/RoleListView.tsx
import { jsx as jsx22, jsxs as jsxs18 } from "react/jsx-runtime";
function RoleListView({
  roles,
  loading = false,
  createHref = "/admin/roles/new",
  editHref = (role) => `/admin/roles/${role.id}`
}) {
  return /* @__PURE__ */ jsxs18("div", { className: "wh-ui", children: [
    /* @__PURE__ */ jsx22(
      PageHeader,
      {
        title: "Roles & permissions",
        description: "RBAC roles with permission strings such as site.list.",
        actions: /* @__PURE__ */ jsx22("a", { href: createHref, children: /* @__PURE__ */ jsx22(Button, { children: "New role" }) })
      }
    ),
    /* @__PURE__ */ jsx22(
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
            render: (row) => /* @__PURE__ */ jsx22("div", { className: "flex flex-wrap gap-1", children: row.permissions.map((permission) => /* @__PURE__ */ jsx22(Badge, { tone: "accent", children: permission }, permission)) })
          },
          {
            key: "actions",
            header: "",
            render: (row) => /* @__PURE__ */ jsx22("a", { href: editHref(row), className: "text-[var(--wh-color-accent)] underline", children: "Edit" })
          }
        ]
      }
    )
  ] });
}

// src/admin/pages/LoginPage.tsx
import { jsx as jsx23 } from "react/jsx-runtime";
function LoginPage({
  action,
  csrfToken,
  csrfFieldName,
  emailDefault,
  error
}) {
  return /* @__PURE__ */ jsx23(
    LoginForm,
    {
      action,
      csrfToken,
      csrfFieldName,
      emailDefault: emailDefault || "",
      error: error || void 0
    }
  );
}

// src/admin/pages/AdminDashboard.tsx
import { jsx as jsx24, jsxs as jsxs19 } from "react/jsx-runtime";
function AdminDashboard({
  userLabel,
  navItems,
  siteCount = 0,
  hostCount = 0,
  flashes
}) {
  return /* @__PURE__ */ jsxs19(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Dashboard", children: [
    /* @__PURE__ */ jsx24(FlashList, { flashes }),
    /* @__PURE__ */ jsx24(
      PageHeader,
      {
        title: "Dashboard",
        description: "Multi-tenant control panel powered by @webhemi/ui."
      }
    ),
    /* @__PURE__ */ jsxs19("div", { style: { display: "flex", gap: "1.5rem", flexWrap: "wrap" }, children: [
      /* @__PURE__ */ jsxs19(Alert, { tone: "info", title: "Sites", children: [
        siteCount,
        " configured"
      ] }),
      /* @__PURE__ */ jsxs19(Alert, { tone: "info", title: "Hosts", children: [
        hostCount,
        " configured"
      ] })
    ] })
  ] });
}

// src/admin/pages/SitesPage.tsx
import { jsx as jsx25, jsxs as jsxs20 } from "react/jsx-runtime";
function SitesPage({
  userLabel,
  navItems,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs20(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Sites", children: [
    /* @__PURE__ */ jsx25(FlashList, { flashes }),
    /* @__PURE__ */ jsx25(SiteListView, { sites: sites || [], createHref: "#create-site" }),
    canEdit ? /* @__PURE__ */ jsxs20("form", { id: "create-site", action: createAction, method: "post", style: { marginTop: "2rem" }, children: [
      /* @__PURE__ */ jsx25(FormField, { label: "Name", htmlFor: "name", required: true, children: /* @__PURE__ */ jsx25(Input, { id: "name", name: "name", required: true }) }),
      /* @__PURE__ */ jsx25(FormField, { label: "Slug", htmlFor: "slug", required: true, hint: "Lowercase identifier", children: /* @__PURE__ */ jsx25(Input, { id: "slug", name: "slug", required: true }) }),
      /* @__PURE__ */ jsx25(Button, { type: "submit", children: "Create site" })
    ] }) : null
  ] });
}

// src/admin/pages/HostsPage.tsx
import { jsx as jsx26, jsxs as jsxs21 } from "react/jsx-runtime";
function HostsPage({
  userLabel,
  navItems,
  hosts,
  sites,
  canEdit,
  createAction,
  flashes
}) {
  return /* @__PURE__ */ jsxs21(AdminLayout, { navItems: navItems || [], userLabel, topBarTitle: "Hosts", children: [
    /* @__PURE__ */ jsx26(FlashList, { flashes }),
    /* @__PURE__ */ jsx26(SiteHostListView, { hosts: hosts || [], createHref: "#create-host" }),
    canEdit ? /* @__PURE__ */ jsxs21("form", { id: "create-host", action: createAction, method: "post", style: { marginTop: "2rem" }, children: [
      /* @__PURE__ */ jsx26(FormField, { label: "Hostname", htmlFor: "host", required: true, children: /* @__PURE__ */ jsx26(Input, { id: "host", name: "host", placeholder: "www.example.com", required: true }) }),
      /* @__PURE__ */ jsx26(FormField, { label: "Site", htmlFor: "site_id", required: true, children: /* @__PURE__ */ jsx26(Select, { id: "site_id", name: "site_id", required: true, children: (sites || []).map((site) => /* @__PURE__ */ jsx26("option", { value: site.id, children: site.name }, site.id)) }) }),
      /* @__PURE__ */ jsx26(FormField, { label: "Surface", htmlFor: "surface", children: /* @__PURE__ */ jsxs21(Select, { id: "surface", name: "surface", defaultValue: "site", children: [
        /* @__PURE__ */ jsx26("option", { value: "admin", children: "admin" }),
        /* @__PURE__ */ jsx26("option", { value: "site", children: "site" }),
        /* @__PURE__ */ jsx26("option", { value: "api", children: "api" })
      ] }) }),
      /* @__PURE__ */ jsx26(Button, { type: "submit", children: "Add host" })
    ] }) : null
  ] });
}

// src/themes/default/components/SiteHeader/SiteHeader.tsx
import { jsx as jsx27, jsxs as jsxs22 } from "react/jsx-runtime";
function SiteHeader({ siteName, navItems = [], actions, className }) {
  return /* @__PURE__ */ jsx27(
    "header",
    {
      className: cn(
        "wh-ui border-b border-[var(--wh-color-line)] bg-[var(--wh-color-surface)]",
        className
      ),
      children: /* @__PURE__ */ jsxs22("div", { className: "mx-auto flex max-w-5xl items-center justify-between gap-6 px-6 py-4", children: [
        /* @__PURE__ */ jsx27(
          "a",
          {
            href: "/",
            className: "font-[family-name:var(--wh-font-display)] text-2xl text-[var(--wh-color-ink)] no-underline",
            children: siteName
          }
        ),
        /* @__PURE__ */ jsx27("nav", { className: "flex flex-1 items-center gap-4", "aria-label": "Primary", children: navItems.map((item) => /* @__PURE__ */ jsx27(
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
        actions ? /* @__PURE__ */ jsx27("div", { className: "flex items-center gap-2", children: actions }) : null
      ] })
    }
  );
}

// src/themes/default/components/Hero/Hero.tsx
import { jsx as jsx28, jsxs as jsxs23 } from "react/jsx-runtime";
function Hero({ title, subtitle, actions, className }) {
  return /* @__PURE__ */ jsxs23(
    "section",
    {
      className: cn(
        "wh-ui relative overflow-hidden bg-[var(--wh-color-ink)] text-[var(--wh-color-surface)]",
        className
      ),
      children: [
        /* @__PURE__ */ jsx28(
          "div",
          {
            className: "pointer-events-none absolute inset-0 opacity-40",
            style: {
              background: "radial-gradient(ellipse at 20% 20%, var(--wh-color-accent) 0%, transparent 55%), radial-gradient(ellipse at 80% 80%, var(--wh-color-accent-hot) 0%, transparent 50%)"
            },
            "aria-hidden": true
          }
        ),
        /* @__PURE__ */ jsxs23("div", { className: "relative mx-auto flex min-h-[70vh] max-w-5xl flex-col justify-end gap-4 px-6 pb-16 pt-24", children: [
          /* @__PURE__ */ jsx28("h1", { className: "max-w-3xl font-[family-name:var(--wh-font-display)] text-5xl leading-tight md:text-6xl", children: title }),
          subtitle ? /* @__PURE__ */ jsx28("p", { className: "max-w-xl text-lg text-[var(--wh-color-canvas)]/90", children: subtitle }) : null,
          actions ? /* @__PURE__ */ jsx28("div", { className: "mt-2 flex flex-wrap gap-3", children: actions }) : null
        ] })
      ]
    }
  );
}
export {
  AdminDashboard,
  AdminLayout,
  Alert,
  Badge,
  Button,
  Checkbox,
  DataTable,
  FlashList,
  FormField,
  Hero,
  HostsPage,
  Icon,
  Input,
  Label,
  LoginForm,
  LoginPage,
  Modal,
  PageHeader,
  Pagination,
  RoleListView,
  Select,
  Sidebar,
  SiteHeader,
  SiteHostListView,
  SiteListView,
  SitesPage,
  TopBar,
  UserListView
};
//# sourceMappingURL=index.js.map