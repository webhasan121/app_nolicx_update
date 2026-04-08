import { router } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import PrimaryButton from "../../../components/PrimaryButton";

export default function RiderConsignmentIndexPage({ riderInfo = {}, orders = [] }) {
    const confirmOrder = (orderId) => {
        router.post(
            route("rider.consignment.confirm", { order: orderId }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AppLayout title="Consignments">
            <Container>
                <div className="flex justify-between items-center p-2">
                    <div>
                        {orders.length ? (
                            <>{orders.length} consignment are available.</>
                        ) : (
                            <>No consignment found !</>
                        )}
                    </div>
                    <div>
                        <div className="inline px-2 py-1 rounded-xl bg-indigo-900 text-white shadow text-sm">
                            <i className="fas fa-location pr-2"></i>{" "}
                            {riderInfo?.targeted_area_name ?? "N/A"}
                        </div>
                    </div>
                </div>

                <Hr />

                <div
                    style={{
                        display: "grid",
                        gridTemplateColumns: "repeat(auto-fit, 160px)",
                        gap: "1rem",
                    }}
                >
                    {orders.map((order) =>
                        order.displayable ? (
                            <div
                                key={order.id}
                                className="bg-white rounded shadow text-center flex flex-col justify-between"
                            >
                                <div className="py-2 bg-gray-200">
                                    <h3 className="text-xs text-gray-500">
                                        Order ID
                                    </h3>
                                    <div className="font-bold">{order.id}</div>
                                </div>

                                <div className="p-2">
                                    <div className="flex justify-center items-center -space-x-2 overflow-hidden">
                                        {order.thumbnails.map((thumbnail, index) => (
                                            <img
                                                key={`${order.id}-${index}`}
                                                src={`/storage/${thumbnail}`}
                                                className="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
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
                                    <div className="text-sm text-gray-500 flex justify-center items-center text-center">
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
                                        <i className="fas fa-map-marker-alt pr-1"></i>
                                        {order.location}
                                    </div>
                                </div>

                                <div className="p-1">
                                    <PrimaryButton
                                        onClick={() => confirmOrder(order.id)}
                                    >
                                        pick{" "}
                                        <div className="px-2 text-xs">
                                            ({order.shipping}TK)
                                        </div>
                                    </PrimaryButton>
                                </div>
                            </div>
                        ) : null,
                    )}
                </div>
            </Container>
        </AppLayout>
    );
}
