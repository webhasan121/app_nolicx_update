import { Link } from "@inertiajs/react";
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
                    <Link href="/user">User Panel</Link>
                    <Link href="/user/orders">Orders</Link>
                    <Link href="/profile">Profile</Link>
                    <Link href={route("logout")}>
                        Logout
                    </Link>
                </div>
            )}
        </div>
    );
}
