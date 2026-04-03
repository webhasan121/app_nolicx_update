import { useEffect, useState } from "react";
import { Link, usePage, router } from "@inertiajs/react";
import Modal from "./Modal";

export default function StickyNav({ open, setOpen }) {
    const { auth } = usePage().props;

    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const onScroll = () => {
            setVisible(window.scrollY > 150);
        };

        window.addEventListener("scroll", onScroll);
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    const user = auth?.user;
    const cartCount = auth?.cartCount ?? 0;

      const [show, setShow] = useState(false);
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [search, setSearch] = useState("");

    const handleSubmit  = (e) => {
        e.preventDefault();
        router.get("/search", { q: search });
        setShow(false);
    };

    return (
        <>
            <div
                className={`fixed top-0 left-0 z-50 w-full py-1 bg-white transition-all duration-300 ${
                    visible ? "block" : "hidden pointer-events-none"
                }`}
            >
                <div className="flex items-center justify-between w-full px-3 mx-auto max-w-8xl">
                    {/* LEFT SIDE */}
                    <div className="flex items-center gap-4">
                        <button
                            className="w-20 px-2 border-r"
                            onClick={() => setOpen(!open)}
                        >
                            {!open ? (
                                <i className="text-lg fas fa-align-justify"></i>
                            ) : (
                                <i className="text-lg fas fa-times"></i>
                            )}
                        </button>

                        <Link href="/" className="flex items-center">
                            <img src="/icon.png" style={{ width: "40px" }} />
                            <div className="text-lg font-bold ps-2">
                                {import.meta.env.VITE_APP_NAME?.toUpperCase()}
                            </div>
                        </Link>
                    </div>

                    {/* RIGHT SIDE */}
                    <div>
                        <div className="flex items-center justify-between">
                            {/* SEARCH */}
                            <button
                                className="mx-2 rounded"
                                onClick={() => setShow(true)}
                            >
                                <i className="p-2 fas fa-search text-md"></i>
                            </button>

                            {user ? (
                                <>
                                    {/* CART */}
                                    <Link href="/cart" className="mr-3">
                                        <button className="flex items-center btn">
                                            <i className="fas fa-cart-plus"></i>
                                            <span className="pb-3 text-green">
                                                {cartCount}
                                            </span>
                                        </button>
                                    </Link>

                                    {/* DROPDOWN */}
                                    <div className="relative flex sm:items-center sm:ms-6">
                                        <button
                                            onClick={() =>
                                                setDropdownOpen(!dropdownOpen)
                                            }
                                            className="flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border rounded-md hover:text-gray-700"
                                        >
                                            <div>
                                                {user.name.length > 8
                                                    ? user.name.substring(
                                                          0,
                                                          8,
                                                      ) + ".."
                                                    : user.name}
                                            </div>

                                            <div className="ms-1">
                                                <svg
                                                    className="w-4 h-4 fill-current"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    />
                                                </svg>
                                            </div>
                                        </button>

                                        {dropdownOpen && (
                                            <div className="absolute right-0 z-50 w-48 mt-2 bg-white border rounded-md shadow-lg">
                                                <Link
                                                    href="/user"
                                                    className="block px-4 py-2 hover:bg-gray-100"
                                                >
                                                    User Panel
                                                </Link>

                                                <Link
                                                    href="/upgrade/vendor"
                                                    className="block px-4 py-2 hover:bg-gray-100"
                                                >
                                                    Request Vendor
                                                </Link>

                                                <Link
                                                    href="/upgrade/reseller"
                                                    className="block px-4 py-2 hover:bg-gray-100"
                                                >
                                                    Request Reseller
                                                </Link>

                                                <Link
                                                    href="/dashboard"
                                                    className="block px-4 py-2 hover:bg-gray-100"
                                                >
                                                    Dashboard
                                                </Link>

                                                <Link
                                                    href={route("logout")}
                                                    className="block w-full px-4 py-2 text-left text-red-500 hover:bg-gray-100"
                                                >
                                                    Log Out
                                                </Link>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <Link
                                    href="/login"
                                    className="px-3 uppercase text-md"
                                >
                                    <i className="pr-2 fas fa-sign-in"></i>
                                    Login
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* SEARCH MODAL */}
            <>
                <Modal show={show} onClose={() => setShow(false)}>
                    <div className="p-3">
                        <form onSubmit={handleSubmit}>
                            <input
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search Product By Title or Tags"
                                className="w-full border rounded-md"
                            />
                            <hr className="my-2" />
                            <button
                                type="submit"
                                className="px-4 py-2 text-white bg-indigo-600 rounded-md"
                            >
                                Search
                            </button>
                        </form>
                    </div>
                </Modal>
            </>
        </>
    );
}
