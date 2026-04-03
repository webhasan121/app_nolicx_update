import { usePage } from "@inertiajs/react";

export default function HasRole({ name, children }) {
  const user = usePage().props?.auth?.user;
  const roles = user?.roles?.map((r) => r.name) || [];
  if (!name || !roles.includes(name)) return null;
  return <>{children}</>;
}

