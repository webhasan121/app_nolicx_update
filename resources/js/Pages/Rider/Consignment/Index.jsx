import { router } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import PrimaryButton from "../../../components/PrimaryButton";
import useTranslation from "../../../hooks/useTranslation";

export default function RiderConsignmentIndexPage({ riderInfo = {}, orders = [], assignedConsignments = [] }) {
    const { t } = useTranslation();

    const confirmOrder = (orderId) => {
        router.post(
            route("rider.consignment.confirm", { order: orderId }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const changeStatus = (id, status) => {
        router.post(
            route("rider.consignment.status", { consignment: id }),
            { status },
            {
                preserveScroll: true,
            },
        );
    };

    const totalConsignments = orders.length + assignedConsignments.length;

    return (
        <AppLayout title={t("Consignments")}>
            <Container>
                <div className="flex items-center justify-between p-2">
                    <div>
                        {totalConsignments ? (
                            <>{totalConsignments} consignment are available.</>
                        ) : (
                            <>No consignment found !</>
                        )}
                    </div>
                    <div>
                        <div className="inline px-2 py-1 text-sm text-white bg-indigo-900 shadow rounded-xl">
                            <i className="pr-2 fas fa-location"></i>{" "}
                            {riderInfo?.targeted_area_name ?? "N/A"}
                        </div>
                    </div>
                </div>

                <Hr />

                {orders.length ? (
                    <div className="mb-3 font-semibold">Available consignments</div>
                ) : null}

                <div className="grid gap-4" style={{ gridTemplateColumns: "repeat(auto-fit, 160px)" }}>
                    {orders.map((order) => (
                            <div
                                key={order.id}
                                className="flex flex-col justify-between text-center bg-white rounded shadow"
                            >
                                <div className="py-2 bg-gray-200">
                                    <h3 className="text-xs text-gray-500">{t("Order ID")}</h3>
                                    <div className="font-bold">{order.id}</div>
                                </div>

                                <div className="p-2">
                                    <div className="flex items-center justify-center -space-x-2 overflow-hidden">
                                        {order.thumbnails.map((thumbnail, index) => (
                                            <img
                                                key={`${order.id}-${index}`}
                                                src={`/storage/${thumbnail}`}
                                                className="inline-block rounded-full size-10 ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                                alt=""
                                            />
                                        ))}
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    <div className="text-4xl font-bold">
                                        <sup>Tk</sup>
                                        {order.display_total}
                                    </div>
                                    <div className="flex items-center justify-center text-sm text-center text-gray-500">
                                        <div className="pl-1 font-bold">
                                            {order.total_for_not_resel ?? "N/A"}
                                        </div>
                                        <div
                                            className="px-1"
                                            style={{ lineHeight: "8px" }}
                                        >
                                            +
                                        </div>
                                        <div className="flex justify-center items-cenrer">
                                            <div>
                                                {order.system_comission ?? "N/A"}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    <div className="text-xs text-gray-500">
                                        <i className="pr-1 fas fa-map-marker-alt"></i>
                                        {order.location}
                                    </div>
                                </div>

                                <div className="p-1">
                                    <PrimaryButton
                                        onClick={() => confirmOrder(order.id)}
                                    >{t("pick")}{" "}
                                        <div className="px-2 text-xs">
                                            ({order.shipping}{t("TK)")}</div>
                                    </PrimaryButton>
                                </div>
                            </div>
                    ))}
                </div>

                {assignedConsignments.length ? (
                    <div className="mt-6 mb-3 font-semibold">Assigned consignments</div>
                ) : null}

                <div className="grid gap-4" style={{ gridTemplateColumns: "repeat(auto-fit, 160px)" }}>
                    {assignedConsignments.map((cod) => (
                        <div
                            key={cod.id}
                            className="flex flex-col justify-between text-center bg-white rounded shadow"
                        >
                            <div className="py-2 bg-gray-200">
                                <h3 className="text-xs text-gray-500">
                                    Order ID{" "}
                                    <a
                                        href={route("rider.consignment.view", { id: cod.id })}
                                        className="inline-block px-2 text-xs text-white bg-indigo-900 shadow rounded-xl"
                                    >
                                        View
                                    </a>
                                </h3>
                                <div className="font-bold">{cod.order_id}</div>
                            </div>

                            <div className="p-2">
                                <div className="flex items-center justify-center -space-x-2 overflow-hidden">
                                    {cod.images.map((image, index) => (
                                        <img
                                            key={`${cod.id}-${index}`}
                                            src={`/storage/${image}`}
                                            className="inline-block rounded-full size-10 ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                            alt=""
                                        />
                                    ))}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <div className="text-2xl font-bold">{cod.display_total} Tk</div>
                                <div className="text-sm text-gray-500">
                                    {cod.total_for_not_resel ?? "N/A"} + {cod.system_comission ?? "N/A"}
                                </div>
                                <div className="text-xs text-red-500">
                                    Commission {cod.system_comission ?? "N/A"}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <p className="text-xs">{cod.created_at_formatted}</p>
                                <div className="text-xs text-gray-500">
                                    <i className="pr-1 fas fa-map-marker-alt"></i>
                                    {cod.location ?? "N/A"}
                                </div>
                            </div>

                            {cod.status === "Pending" ? (
                                <div className="pb-2">
                                    <button
                                        className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                        onClick={() => changeStatus(cod.id, "Received")}
                                    >
                                        Mark as Received
                                    </button>
                                </div>
                            ) : null}

                            {cod.status === "Received" ? (
                                <div className="pb-2">
                                    <button
                                        className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                        onClick={() => changeStatus(cod.id, "Completed")}
                                    >
                                        Mark as Delivered
                                    </button>
                                </div>
                            ) : null}

                            {cod.status === "Completed" ? (
                                <p className="p-2 font-bold text-green-900 bg-green-200">
                                    <i className="fas fa-check-circle ps-2"></i> Earn ({cod.shipping}TK)
                                </p>
                            ) : null}
                        </div>
                    ))}
                </div>
            </Container>
        </AppLayout>
    );
}
