import { usePage } from "@inertiajs/react";
import React from "react";
import ApplicationName from "../ApplicationName";
import NavLink from "../NavLink";

export default function Footer() {
    const email = "support@example.com"; // config('app.support_mail')
    const { auth, global } = usePage().props;

    const branches = global?.branches || [];



    const widgets = [
        {
            head: "Menu",
            menu: [
                { title: "About Us", route: "/page/about-us" },
                { title: "Contact Us", route: "/page/about-us" },
                { title: "Products", route: "/products" },
                { title: "Categories", route: "/category" },
            ],
        },
        {
            head: "Links",
            menu: [
                { title: "Earn", route: "/page/how-to-earn" },
                { title: "Privacy Policy", route: "/page/privacy-policy" },
                { title: "Return & Refund", route: "/page/return-refund" },
                {
                    title: "Terms & Conditions",
                    route: "/page/terms-conditions",
                },
            ],
        },
        auth.user
            ? {
                  head: "Account",
                  menu: [
                      { title: "Dashboard", route: "/dashboard" },
                      { title: "Profile", route: "/profile" },
                  ],
              }
            : {
                  head: "Account",
                  menu: [
                      { title: "Login", route: "/login" },
                      { title: "Register", route: "/register" },
                  ],
              },
    ];

    const openMail = (e) => {
        e.preventDefault();

        if (/Mobi|Android|iPhone/i.test(navigator.userAgent)) {
            window.location.href = `mailto:${email}`;
        } else {
            window.open(
                `https://mail.google.com/mail/?view=cm&fs=1&to=${email}`,
                "_blank",
            );
        }
    };

    return (
        <footer>
            {/* Top Section */}
            <section className="px-6 pt-16 pb-8 mx-auto mb-8 border-b max-w-7xl">
                <div className="flex flex-col gap-8 lg:flex-row lg:gap-16">
                    {/* Logo + Playstore */}
                    <div className="flex flex-row lg:flex-col items-center lg:items-start md:w-[25%]">
                        <NavLink
                            href="/"
                            className="flex items-center w-full border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                        >
                            <img
                                height="50"
                                width="60"
                                src="/icon.png"
                                alt="logo"
                            />
                            <div className="text-4xl font-bold ps-2">
                                <ApplicationName />
                            </div>
                        </NavLink>

                        <NavLink
                            href="https://play.google.com"
                            className="w-[150px] md:w-[225px] lg:w-full border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                            target="_blank"
                            rel="noreferrer"
                        >
                            <img src="/playstore.png" alt="playstore" />
                        </NavLink>
                    </div>

                    {/* Widgets */}
                    <div className="grid w-full grid-cols-2 gap-6 lg:grid-cols-4">
                        {widgets.map((widget, index) => (
                            <div key={index} className="block">
                                {widget.head && (
                                    <h5 className="mb-4 text-lg font-bold border-b">
                                        {widget.head}
                                    </h5>
                                )}

                                <ul className="mb-4">
                                    {widget.menu.map((link, i) => (
                                        <li key={i}>
                                            <NavLink
                                                href={link.route}
                                                className="block py-1 mb-1 border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                                            >
                                                {link.title}
                                            </NavLink>
                                        </li>
                                    ))}
                                </ul>

                                {index === widgets.length - 1 && (
                                    <NavLink
                                        href="/"
                                        onClick={openMail}
                                        className="p-2 px-4 rounded-md btn_outline_secondary bold border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                                    >
                                        <i className="mr-2 fa-solid fa-paper-plane"></i>
                                        <span>Mail Us</span>
                                    </NavLink>
                                )}
                            </div>
                        ))}

                        {/* Information Block */}
                        <div className="block">
                            <h5 className="mb-4 text-lg font-bold border-b">
                                Information
                            </h5>

                            <div className="space-y-4">
                                <p>
                                    <strong>DBID No</strong> :{" "}
                                    <span>123456</span>
                                </p>
                                <p>
                                    <strong>Trade License</strong> :{" "}
                                    <span>TR-987654</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Branch Section */}
            <section className="mb-16 max-w-7xl">
                <div className="grid gap-8 lg:grid-cols-3 ml-14">
                    {branches?.map((branch) => (
                        <div key={branch.id} className="block">
                            <h4 className="mb-4 text-2xl font-bold text-blue-600 uppercase">
                                {branch.name}
                            </h4>

                            <p>
                                <i className="w-6 mr-2 fa-solid fa-map-marker-alt"></i>
                                : {branch.address}
                            </p>

                            <p className="my-2">
                                <i className="w-6 mr-2 fa-solid fa-phone"></i>:{" "}
                                {branch.phone}
                            </p>

                            <p>
                                <i className="w-6 mr-2 fa-solid fa-envelope"></i>
                                : {branch.email}
                            </p>
                        </div>
                    ))}
                </div>
            </section>

            {/* Bottom Copyright */}
            <section className="px-6 py-4 text-center bg-gray-800">
                <p className="text-base text-white">
                    © 2025 All Rights Reserved
                </p>
            </section>
        </footer>
    );
}
