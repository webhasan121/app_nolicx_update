import NavLink from "../../../../components/NavLink";
import SectionHeader from "../../../../components/dashboard/section/Header";

export default function VendorNavigation({ vendor, activeRoute }) {
    return (
        <div>
            <SectionHeader
                title={`${vendor?.shop_name_en ?? "N/A"} / ${vendor?.shop_name_bn ?? "N/a"}`}
                content={
                    <div>
                        <div className="text-sm">
                            <NavLink
                                href={route("system.users.edit", {
                                    id: vendor?.user?.id,
                                })}
                            >
                                {vendor?.user?.name ?? "N/A"}
                            </NavLink>
                            {" - "}
                            {vendor?.status ?? "N/A"}
                        </div>
                        <span className="text-xs text-gray-400">
                            {vendor?.created_at_formatted ?? ""}
                        </span>
                    </div>
                }
            />
            <br />
            <NavLink
                active={activeRoute === "system.vendor.edit"}
                href={route("system.vendor.edit", { id: vendor?.id })}
            >
                User
            </NavLink>
            <NavLink
                active={activeRoute === "system.vendor.settings"}
                href={route("system.vendor.settings", { id: vendor?.id })}
            >
                Settings
            </NavLink>
            <NavLink
                active={activeRoute === "system.vendor.documents"}
                href={route("system.vendor.documents", { id: vendor?.id })}
            >
                Documents
            </NavLink>
            <NavLink
                active={activeRoute === "system.products.index"}
                href={route("system.products.index", {
                    find: vendor?.id,
                    from: "vendor",
                })}
            >
                Products
            </NavLink>
        </div>
    );
}
