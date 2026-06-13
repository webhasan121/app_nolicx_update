import { Head, usePage } from "@inertiajs/react";
import { useEffect } from "react";
import Swal from "sweetalert2";
import AutoTranslate from "../components/AutoTranslate";
import SupportButton from "../components/SupportButton";
import Navigation from "../layout/Navigation";
import ResponsiveNavigation from "./ResponsiveNavigation";
import VendorResponsiveNavigation from "./VendorResponsiveNavigation";
import ResellerResponsiveNavigation from "./ResellerResponsiveNavigation";
import RiderResponsiveNavigation from "./RiderResponsiveNavigation";

export default function AppLayout({ children, header, title = "Dashboard" }) {
    const { flash, appConfig, auth, roles = [] } = usePage().props;
    const user = auth?.user;
    const roleNames = roles.length
        ? roles
        : user?.roles?.map((role) => role.name) ?? [];
    const currentNav = user?.active_nav ?? "";

    useEffect(() => {
        if (flash?.info) {
            Swal.fire({
                title: "Attention",
                text: flash.info,
                icon: "info",
                confirmButtonText: "OK",
            });
        }

        if (flash?.success) {
            Swal.fire({
                title: "Done",
                text: flash.success,
                icon: "success",
                confirmButtonText: "OK",
            });
        }

        if (flash?.warning) {
            Swal.fire({
                title: "Alart !",
                text: flash.warning,
                icon: "warning",
                confirmButtonText: "OK",
            });
        }

        if (flash?.error) {
            Swal.fire({
                title: "Error !",
                text: flash.error,
                icon: "error",
                confirmButtonText: "Close",
            });
        }
    }, [flash]);

    return (
        <div className="h-screen overflow-hidden font-sans antialiased bg-gray-100">
            <AutoTranslate />
            <Head title={title} />

            <style
                dangerouslySetInnerHTML={{
                    __html: `
                        tr:hover { background-color: #f3f4f6; }
                        tr:nth-child(even) { background-color: #f9fafb; }
                        .discount-badge {
                            position: absolute;
                            top: 0;
                            left: 0;
                            color: white;
                            font-weight: bold;
                            padding: 3px 8px;
                            clip-path: polygon(0px 0px, 85px 0px, 0px 75px);
                            width: 100px;
                            height: 100px;
                            text-align: center;
                            display: flex;
                            font-size: 15px;
                            padding-top: 8px;
                        }
                    `,
                }}
            />

            <SupportButton whatsapp={appConfig?.whatsapp_no} />
            <div className="fixed top-0 left-0 right-0 z-50">
                <Navigation />
            </div>

            <div className="h-screen pt-16">
                <div className="flex h-full overflow-hidden sm:pl-6 lg:pl-8">
                    <div
                        className="hidden h-full shrink-0 overflow-y-auto md:block"
                        style={{ width: 220 }}
                    >
                        <div className="w-full pt-2 pb-3">
                            <ResponsiveNavigation />
                            {roleNames.includes("vendor") &&
                                currentNav === "vendor" && (
                                    <VendorResponsiveNavigation />
                                )}
                            {roleNames.includes("reseller") &&
                                currentNav === "reseller" && (
                                    <ResellerResponsiveNavigation />
                                )}
                            {roleNames.includes("rider") &&
                                currentNav === "rider" && (
                                    <RiderResponsiveNavigation />
                                )}
                        </div>
                    </div>

                    <div className="w-full h-full overflow-x-hidden overflow-y-auto">
                        {header && (
                            <header className="">
                                <div className="w-full px-2 px-4 py-6 mx-auto sm:px-6 lg:px-8">
                                    {header}
                                </div>
                            </header>
                        )}
                        <main>{children}</main>
                    </div>
                </div>
            </div>
        </div>
    );
}
