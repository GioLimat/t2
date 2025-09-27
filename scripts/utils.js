export function q(sel, root = document) {
  return root.querySelector(sel);
}
export function qa(sel, root = document) {
  return Array.from(root.querySelectorAll(sel));
}
export function formatMoney(v) {
  return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(
    Number(v || 0)
  );
}
export function go(href) {
  location.href = href;
}
