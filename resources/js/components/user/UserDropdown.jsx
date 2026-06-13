import NavLink from "../NavLink";
import { useState } from "react";
import useTranslation from "../../hooks/useTranslation";

export default function UserDropdown({ user }) {
    const [open, setOpen] = useState(false);
    const { t } = useTranslation();

    return (
        <div className="relative">
            <button onClick={() => setOpen(!open)}>
                {user.name.substring(0, 8)}
            </button>

            {open && (
                <div className="absolute right-0 w-48 bg-white shadow-md">
                    <NavLink href="/user">{t("User Panel")}</NavLink>
                    <NavLink href="/user/orders">{t("Orders")}</NavLink>
                    <NavLink href="/profile">{t("Profile")}</NavLink>
                    <NavLink href={route("logout")}>{t("Logout")}</NavLink>
                </div>
            )}
        </div>
    );
}
