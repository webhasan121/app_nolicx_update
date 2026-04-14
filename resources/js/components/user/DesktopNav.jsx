import { usePage } from "@inertiajs/react";
import { useState } from "react";
import NavLink from "../NavLink";
import UserDropdown from "./UserDropdown";

export default function DesktopNav() {
    const { auth } = usePage().props;
    const [open, setOpen] = useState(false);

    return (
        <div className="text-center bg-white">
            <div className="flex items-center justify-between px-4 py-2">

                {/* Left */}
                <div className="flex items-center gap-4">
                    <button onClick={() => setOpen(!open)}>
                        <i className="fas fa-align-justify"></i>
                    </button>

                    <NavLink
                        href="/"
                        className="flex items-center border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                    >
                        <img src="/icon.png" width="50" />
                        <div className="pl-2 font-bold">Nolicx</div>
                    </NavLink>
                </div>

                {/* Search */}
                <div className="flex-1 hidden px-4 md:flex">
                    <form action="/search" className="w-full max-w-xl">
                        <input
                            type="search"
                            name="q"
                            placeholder="Search Product..."
                            className="w-full border rounded-md"
                        />
                    </form>
                </div>

                {/* Right */}
                <div className="flex items-center gap-4">
                    {auth.user ? (
                        <>
                            <NavLink
                                href={route("carts.view")}
                                className="border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                            >
                                <i className="fas fa-cart-plus"></i>
                                <span>{auth.cartCount}</span>
                            </NavLink>

                            <UserDropdown user={auth.user} />
                        </>
                    ) : (
                        <NavLink
                            href={route("login")}
                            className="border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                        >
                            Login
                        </NavLink>
                    )}
                </div>
            </div>
        </div>
    );
}
