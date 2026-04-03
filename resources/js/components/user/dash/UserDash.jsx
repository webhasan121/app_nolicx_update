import { Link, usePage } from "@inertiajs/react";
import { useEffect } from "react";
import Swal from "sweetalert2";
import SupportButton from "../../SupportButton";
import Header from "../dash/Header";
import Container from "../../dashboard/Container";
import NavLink from "../../NavLink";

export default function UserDash({ children }) {
    const { auth, flash, appConfig } = usePage().props;
    const user = auth?.user;
    const roles = user?.roles?.map((r) => r.name) ?? [];
    const activeNav = user?.active_nav;

    // Flash Message SweetAlert
    useEffect(() => {
        if (flash?.success) {
            Swal.fire("Success", flash.success, "success");
        }
        if (flash?.warning) {
            Swal.fire("Warning", flash.warning, "warning");
        }
        if (flash?.error) {
            Swal.fire("Error", flash.error, "error");
        }
        if (flash?.info) {
            Swal.fire("Info", flash.info, "info");
        }
    }, [flash]);

    return (
        <div style={{ marginBottom: "100px" }}>
            <style
                dangerouslySetInnerHTML={{
                    __html: `
            body {
              background-color: #f0f0f0 !important;
            }

            thead {
              background-color: rgb(238, 238, 238) !important;
            }

            th {
              vertical-align: middle !important;
              font-size: 14px;
            }

            tr:nth-child(even) {
              background-color: rgb(238, 238, 238);
            }

            #user_asside {
              width: 250px !important;
              height: auto;
            }

            #user_asside .asside_link {
              display: flex;
              padding: 15px;
              margin: 1px 0px;
              cursor: pointer;
            }

            #user_asside .asside_link:hover {
              color: var(--brand-secondary);
            }

            #user_asside .asside_link .fas {
              width: 25px;
              text-align: center;
            }

            .active {
              color: var(--brand-secondary) !important;
              font-weight: bold;
            }

            @media (max-width: 767.98px) {
              #user_asside {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 100% !important;
                display: flex;
                justify-content: space-evenly;
                align-items: center;
                height: 50px;
                background-color: #fff !important;
                z-index: 99999;
              }

              #user_asside .asside_link {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: 0;
                margin: 0px !important;
                padding: 12px 5px !important;
              }
            }
          `,
                }}
            />

            <SupportButton whatsapp={appConfig?.whatsapp_no} />
            <Header />
            <Container>
                <div className="flex">
                    <div
                        id="user_asside"
                        className="py-3 rounded position-sm-absolute col-md-3"
                    >
                        <NavLink
                            href={route("user.dash")}
                            active={route().current("user.dash")}
                            className="asside_link"
                        >
                            <i className="fas fa-home"></i>
                            <span className="hidden pl-2 md:block">
                                Dashboard
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.orders.view")}
                            active={route().current("user.orders.view")}
                            className="asside_link"
                        >
                            <i className="pr-2 fas fa-shopping-cart"></i>
                            <span className="hidden pl-2 md:block">
                                Order ({user?.my_order_as_user_count ?? 0})
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.vip.index")}
                            active={route().current("user.vip.*")}
                            className="asside_link vip"
                        >
                            <i className="pr-2 fas fa-user-check"></i>
                            <span className="hidden pl-2 md:block">VIP</span>
                        </NavLink>

                        <NavLink
                            href={route("user.wallet.index")}
                            active={route().current("user.wallet.*")}
                            className="asside_link wallet"
                        >
                            <i className="pr-2 fas fa-coins"></i>
                            <span className="hidden pl-2 md:block">Wallet</span>
                        </NavLink>

                        <NavLink
                            href={route("user.developer")}
                            active={
                                route().current("user.developer") ||
                                route().current("user.developer.*")
                            }
                            className="asside_link wallet"
                        >
                            <i className="pr-2 fas fa-coins"></i>
                            <span className="hidden pl-2 md:block">
                                Developer
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.management")}
                            active={
                                route().current("user.management") ||
                                route().current("user.management.*")
                            }
                            className="asside_link wallet"
                        >
                            <i className="pr-2 fas fa-coins"></i>
                            <span className="hidden pl-2 md:block">
                                Management
                            </span>
                        </NavLink>

                        {(roles.includes("reseller") ||
                            roles.includes("vendor")) &&
                            activeNav && (
                                <NavLink
                                    href={route("my-shop", {
                                        user: user?.name,
                                    })}
                                    active={route().current("my-shop")}
                                    className="asside_link shop"
                                >
                                    <i className="pr-2 fas fa-shop"></i>
                                    <span className="hidden pl-2 md:block">
                                        My Shop
                                    </span>
                                </NavLink>
                            )}
                    </div>
                    <div
                        id="user_content"
                        className="col-md-9 py-2 p-lg-3 w-full mb-[50px]"
                    >
                        {children}
                    </div>
                </div>
            </Container>
        </div>
    );
}
