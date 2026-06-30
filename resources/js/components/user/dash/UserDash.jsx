import { Head, usePage } from "@inertiajs/react";
import { useEffect } from "react";
import Swal from "sweetalert2";
import SupportButton from "../../SupportButton";
import Header from "../dash/Header";
import NavLink from "../../NavLink";
import useTranslation from "../../../hooks/useTranslation";

const USER_PAGE_TITLES = [
    { title: "Dashboard", matches: ["user.dash"] },
    { title: "Orders", matches: ["user.orders.*"] },
    { title: "VIP", matches: ["user.vip.*", "user.package.*"] },
    { title: "Wallet", matches: ["user.wallet.*", "user.withdraw.*"] },
    { title: "Developer", matches: ["user.developer", "user.developer.*"] },
    { title: "My Shop", matches: ["my-shop", "my-shop.*"] },
    { title: "Management", matches: ["user.management", "user.management.*"] },
    { title: "Management TM", matches: ["user.management-team", "user.management-team.*"] },
    { title: "Profile", matches: ["edit.profile", "edit.profile.*"] },
    { title: "Carts", matches: ["carts.view", "user.carts.*", "cart.qty.*"] },
    { title: "Referral", matches: ["user.ref.*"] },
    { title: "Notices", matches: ["dashboard.notices.*"] },
    { title: "Upgrade Vendor", matches: ["upgrade.vendor.*"] },
    { title: "Upgrade Rider", matches: ["upgrade.rider.*"] },
];

function activeUserPageTitle() {
    return USER_PAGE_TITLES.find((item) =>
        item.matches.some((pattern) => route().current(pattern))
    )?.title ?? "Dashboard";
}

export default function UserDash({ children }) {
    const { auth, flash, appConfig } = usePage().props;
    const { t } = useTranslation();
    const user = auth?.user;
    const roles = user?.roles?.map((r) => r.name) ?? [];
    const activeNav = user?.active_nav;

    // Flash Message SweetAlert
    useEffect(() => {
        if (flash?.success) {
            Swal.fire(t("Success"), flash.success, "success");
        }
        if (flash?.warning) {
            Swal.fire(t("Warning"), flash.warning, "warning");
        }
        if (flash?.error) {
            Swal.fire(t("Error"), flash.error, "error");
        }
        if (flash?.info) {
            Swal.fire(t("Info"), flash.info, "info");
        }
    }, [flash]);

    return (
        <div className="min-h-screen overflow-x-hidden md:h-screen md:overflow-hidden">
            <Head title={activeUserPageTitle()} />
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
            <div className="fixed top-0 left-0 right-0 z-50">
                <Header />
            </div>
            <div className="min-h-screen pt-16 md:h-screen">
                <div className="w-full px-3 sm:px-6 lg:px-8">
                <div className="flex min-h-[calc(100vh-5.5rem)] flex-col overflow-visible md:h-[calc(100vh-5.5rem)] md:flex-row md:overflow-hidden">
                    <div
                        id="user_asside"
                        className="py-3 rounded position-sm-absolute col-md-3 md:h-full md:overflow-y-auto"
                    >
                        <NavLink
                            href={route("user.dash")}
                            active={route().current("user.dash")}
                            className="asside_link"
                        >
                            <i className="fas fa-home"></i>
                            <span className="hidden pl-2 md:block">
                                {t("Dashboard")}
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.orders.view")}
                            active={route().current("user.orders.view")}
                            className="asside_link"
                        >
                            <i className="pr-2 fas fa-shopping-cart"></i>
                            <span className="hidden pl-2 md:block">
                                {t("Order")} ({user?.my_order_as_user_count ?? 0})
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.vip.index")}
                            active={route().current("user.vip.*")}
                            className="asside_link vip"
                        >
                            <i className="pr-2 fas fa-user-check"></i>
                            <span className="hidden pl-2 md:block">{t("VIP")}</span>
                        </NavLink>

                        <NavLink
                            href={route("user.wallet.index")}
                            active={route().current("user.wallet.*")}
                            className="asside_link wallet"
                        >
                            <i className="pr-2 fas fa-coins"></i>
                            <span className="hidden pl-2 md:block">{t("Wallet")}</span>
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
                                {t("Developer")}
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
                                        {t("My Shop")}
                                    </span>
                                </NavLink>
                            )}

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
                                {t("Management")}
                            </span>
                        </NavLink>

                        <NavLink
                            href={route("user.management-team")}
                            active={
                                route().current("user.management-team") ||
                                route().current("user.management-team.*")
                            }
                            className="asside_link wallet"
                        >
                            <i className="pr-2 fas fa-users-cog"></i>
                            <span className="hidden pl-2 md:block">
                                {t("Management TM")}
                            </span>
                        </NavLink>
                    </div>
                    <div
                        id="user_content"
                        className="col-md-9 w-full py-2 pb-20 md:h-full md:overflow-y-auto md:pb-4 lg:p-3"
                    >
                        {children}
                    </div>
                </div>
                </div>
            </div>
        </div>
    );
}
