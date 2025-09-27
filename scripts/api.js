const KEY_USERS = "sf_users";
const KEY_SERVICES = "sf_services";
const KEY_PROVIDERS = "sf_providers";
function read(key) {
  const s = localStorage.getItem(key);
  return s ? JSON.parse(s) : null;
}
function write(key, v) {
  localStorage.setItem(key, JSON.stringify(v));
}
function id(prefix = "id") {
  return prefix + "_" + Math.random().toString(36).slice(2, 9);
}
function seed() {
  if (!read(KEY_PROVIDERS)) {
    const providers = [
      { id: id("pr"), name: "Carlos Silva", rating: 4.8 },
      { id: id("pr"), name: "Mariana Costa", rating: 4.6 },
      { id: id("pr"), name: "Rafael Gomes", rating: 4.7 },
    ];
    write(KEY_PROVIDERS, providers);
  }
  if (!read(KEY_USERS)) {
    write(KEY_USERS, []);
  }
  if (!read(KEY_SERVICES)) {
    write(KEY_SERVICES, []);
  }
}
seed();
export function createUser(user) {
  const users = read(KEY_USERS) || [];
  users.push(user);
  write(KEY_USERS, users);
}
export function findUserByEmail(email) {
  const users = read(KEY_USERS) || [];
  return users.find((u) => u.email === email) || null;
}
export function saveCurrentUser(email) {
  localStorage.setItem("sf_current", email);
}
export function getCurrentUser() {
  const email = localStorage.getItem("sf_current");
  if (!email) return null;
  return findUserByEmail(email);
}
export function getProviders() {
  return read(KEY_PROVIDERS) || [];
}
export function getServices() {
  return read(KEY_SERVICES) || [];
}
export function getServicesByClient(email) {
  const u = findUserByEmail(email);
  if (!u) return [];
  const arr = getServices();
  return arr.filter((s) => s.clientEmail === email);
}
export function getServicesByProvider(providerId) {
  const arr = getServices();
  return arr.filter((s) => s.providerId === providerId);
}
export function allocateService(payload) {
  const services = getServices();
  const providers = getProviders();
  const counts = providers.map((p) => {
    const assigned = services.filter((s) => s.providerId === p.id && s.status !== "Concluído");
    return { id: p.id, name: p.name, count: assigned.length };
  });
  counts.sort((a, b) => a.count - b.count);
  const chosen = counts[0] || providers[0];
  const service = {
    id: id("svc"),
    clientEmail: payload.clientEmail,
    serviceType: payload.serviceType,
    description: payload.description || "",
    price: payload.price,
    providerId: chosen.id,
    providerName: (getProviders().find((x) => x.id === chosen.id) || {}).name || "Prestador",
    status: "Alocado",
    createdAt: Date.now(),
  };
  services.push(service);
  write(KEY_SERVICES, services);
  return service;
}
export function updateServiceStatus(serviceId, newStatus) {
  const services = getServices();
  const svc = services.find((s) => s.id === serviceId);
  if (!svc) return null;
  if (newStatus === "Em execução") {
    const providerServices = services.filter(
      (s) => s.providerId === svc.providerId && s.status === "Em execução"
    );
    if (providerServices.length > 0)
      return { error: "Já existe serviço em execução para este prestador." };
  }
  svc.status = newStatus;
  write(KEY_SERVICES, services);
  if (newStatus === "Concluído") {
    const user = findUserByEmail(svc.clientEmail);
    if (user) {
      const credit = (Number(svc.price) || 0) * 0.05;
      user.credit = (user.credit || 0) + credit;
      const users = read(KEY_USERS) || [];
      const idx = users.findIndex((u) => u.email === user.email);
      if (idx > -1) users[idx] = user;
      write(KEY_USERS, users);
    }
  }
  return svc;
}
export function addRating(serviceId, rating, text) {
  const services = getServices();
  const svc = services.find((s) => s.id === serviceId);
  if (!svc) return null;
  svc.rating = rating;
  svc.review = text;
  write(KEY_SERVICES, services);
  return svc;
}
