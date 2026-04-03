import InputLabel from "./InputLabel";
import TextInput from "./TextInput";

const GROUPS = [
  { title: "Role", prefix: "role_" },
  { title: "Access", prefix: "access" },
  { title: "Sync", prefix: "sync" },
  { title: "Admin", prefix: "admin" },
  { title: "Vendors", prefix: "vendors" },
  { title: "Resellers", prefix: "reseller" },
  { title: "Riders", prefix: "riders" },
  { title: "Shops", prefix: "shop" },
  { title: "Users", prefix: "users" },
  { title: "Product", prefix: "product" },
  { title: "Category", prefix: "category" },
  { title: "Comission", prefix: "comission" },
  { title: "Withdraw", prefix: "withdraw" },
  { title: "VIP Package", prefix: "vip" },
  { title: "VIP User", prefix: "vip_user" },
  { title: "Store", prefix: "store" },
  { title: "Order", prefix: "order" },
  { title: "Slider", prefix: "slider" },
  { title: "Deposit", prefix: "deposit" },
  { title: "Partnership", prefix: "partnership" },
];

export default function PermissionsToUser({
  permissions = [],
  userPermissions = [],
  selectedPermissions = [],
  onChange,
}) {
  const selected = Array.isArray(selectedPermissions) && selectedPermissions.length
    ? selectedPermissions
    : userPermissions;

  const isChecked = (name) => selected?.includes(name);

  const handleChange = (name, checked) => {
    if (!onChange) return;
    let next = [...(selected || [])];
    if (checked) {
      if (!next.includes(name)) next.push(name);
    } else {
      next = next.filter((n) => n !== name);
    }
    onChange(next);
  };

  return (
    <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(250px, 1fr))", gap: "15px" }}>
      {GROUPS.map((group) => (
        <div key={group.title}>
          <InputLabel>{group.title}</InputLabel>
          {permissions
            .filter((p) => String(p.name || "").startsWith(group.prefix))
            .map((permission) => {
              const name = permission.name;
              const id = `perm_${permission.id}`;
              return (
                <div key={permission.id}>
                  <TextInput
                    className="m-0"
                    type="checkbox"
                    name="permissions[]"
                    id={id}
                    value={name}
                    checked={isChecked(name)}
                    onChange={(e) => handleChange(name, e.target.checked)}
                  />
                  <label className="pl-3 text-sm" htmlFor={id}>
                    {name}
                  </label>
                </div>
              );
            })}
        </div>
      ))}
    </div>
  );
}

