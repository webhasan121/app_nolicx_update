import NavLink from "../NavLink";
import { useState } from "react";

export default function UserDropdown({ user }) {
    const [open, setOpen] = useState(false);

    return (
        <div className="relative">
            <button onClick={() => setOpen(!open)}>
                {user.name.substring(0, 8)}
            </button>

            {open && (
                <div className="absolute right-0 w-48 bg-white shadow-md">
                    <NavLink href="/user">User Panel</NavLink>
                    <NavLink href="/user/orders">Orders</NavLink>
                    <NavLink href="/profile">Profile</NavLink>
                    <NavLink href={route("logout")}>Logout</NavLink>
                </div>
            )}
        </div>
    );
}
