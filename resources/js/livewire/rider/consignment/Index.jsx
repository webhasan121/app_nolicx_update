import { router } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import Modal from "../../../components/Modal";
import PrimaryButton from "../../../components/PrimaryButton";

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(([, value]) => value !== "" && value !== null && value !== undefined)
    );
}

function updateFilter(filters, updates) {
    router.get(route("dashboard"), buildQuery(filters, { ...updates }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

export default function Index({ riderConsignmentIndex }) {
    const [open, setOpen] = useState(false);
    const filters = riderConsignmentIndex?.filters ?? {};
    const consignments = riderConsignmentIndex?.consignments ?? [];
    const totals = riderConsignmentIndex?.totals ?? {};

    const changeStatus = (id, status) => {
        router.post(
            route("rider.consignment.status", { consignment: id }),
            { status },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    return (
        <div>
            <Container>
                <div className="flex justify-between items-center">
                    <div className="flex gap-2 items-center">
                        <select
                            value={filters.status ?? "All"}
                            onChange={(e) => updateFilter(filters, { status: e.target.value })}
                            className="py-1 mt-1 rounded"
                            id="select_status"
                        >
                            <option value="All"> -- All -- </option>
                            <option value="Pending">Pending</option>
                            <option value="Received">Received</option>
                            <option value="Completed">Delivered</option>
                            <option value="Returned">Returned</option>
                        </select>
                    </div>

                    <div>
                        <PrimaryButton type="button" onClick={() => setOpen(true)}>
                            <i className="fas fa-filter"></i>
                        </PrimaryButton>
                    </div>
                </div>

                {consignments.length > 0 ? (
                    <>
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns: "repeat(auto-fit, 160px)",
                                gap: "1rem",
                            }}
                        >
                            {consignments.map((cod) => (
                                <div key={cod.id} className="relative bg-white rounded shadow text-center flex flex-col justify-between">
                                    <div className="py-2 bg-gray-200">
                                        <h3 className="text-xs text-gray-500">
                                            Order ID
                                            <a
                                                href={route("rider.consignment.view", { id: cod.id })}
                                                className="cursor-pointer text-xs px-2 inline-block rounded-xl bg-indigo-900 text-white shadow"
                                            >
                                                View
                                            </a>
                                        </h3>
                                        <div className="font-bold">{cod.order_id}</div>
                                    </div>

                                    <div className="p-2">
                                        <div className="flex justify-center items-center -space-x-2 overflow-hidden">
                                            {cod.images.map((image, index) => (
                                                <img
                                                    key={`${cod.id}-${index}`}
                                                    src={`/storage/${image}`}
                                                    className="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                                    alt=""
                                                />
                                            ))}
                                        </div>
                                    </div>

                                    <div className="px-3 py-2">
                                        <div className="text-2xl font-bold flex justify-center ">
                                            {cod.display_total} Tk
                                        </div>
                                        <div className="text-sm text-gray-500 flex justify-center items-center text-center">
                                            <div className="pl-1 font-bold">{cod.total_for_not_resel ?? "N/A"}</div>
                                            <div className="px-1" style={{ lineHeight: "8px" }}>+</div>
                                            <div className="flex justify-center items-cenrer">
                                                <div>{cod.system_comission ?? "N/A"}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="px-3 py-2">
                                        <p className="text-xswwww">{cod.created_at_formatted}</p>
                                        <div className="text-xs text-gray-500">
                                            <i className="fas fa-map-marker-alt pr-1"></i>
                                            {cod.location ?? "N/A"}
                                        </div>
                                    </div>

                                    {cod.status === "Pending" ? (
                                        <>
                                            <div className="pb-2">
                                                <button
                                                    className="rounded border px-2 py-1 bg-indigo-900 text-white shadow text-sm"
                                                    onClick={() => changeStatus(cod.id, "Received")}
                                                >
                                                    Mark as Received
                                                </button>
                                            </div>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="text-xs px-2 rounded-xl bg-white shadow"> Pending </div>
                                            </div>
                                        </>
                                    ) : null}

                                    {cod.status === "Received" ? (
                                        <>
                                            <div className="pb-2">
                                                <button
                                                    className="rounded border px-2 py-1 bg-indigo-900 text-white shadow text-sm"
                                                    onClick={() => changeStatus(cod.id, "Completed")}
                                                >
                                                    Mark as Delivered
                                                </button>
                                            </div>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="text-xs px-2 rounded-xl bg-indigo-200 shadow"> Received </div>
                                            </div>
                                        </>
                                    ) : null}

                                    {cod.status === "Completed" ? (
                                        <>
                                            <p className="p-2 bg-green-200 text-green-900 font-bold">
                                                <i className="fas fa-check-circle ps-2"></i> Earn ({cod.shipping}TK)
                                            </p>
                                            <div className="absolute p-1" style={{ top: 43, left: "50%", transform: "translatex(-50%)" }}>
                                                <div className="text-xs px-2 rounded-xl bg-green-900 text-white shadow"> Done </div>
                                            </div>
                                        </>
                                    ) : null}
                                </div>
                            ))}
                        </div>
                        <table className="w-full border p-2">
                            <tbody>
                                <tr className="p-2">
                                    <td>Delivery</td>
                                    <td>{totals.delivery} TK</td>
                                </tr>
                                <tr className="p-2">
                                    <td>Earn</td>
                                    <td>{totals.earn}</td>
                                </tr>
                            </tbody>
                        </table>
                    </>
                ) : (
                    <p className="bg-gray-50 p-1">No Consignment Found !</p>
                )}

                <Modal show={open} onClose={() => setOpen(false)} maxWidth="md">
                    <div className="p-4 border-b flex justify-between items-center">
                        Filter
                        <div onClick={() => setOpen(false)}>
                            <i className="fas fa-close"></i>
                        </div>
                    </div>
                    <div className="p-4">
                        <div className="md:flex justify-between items-start">
                            <div className="p-2">
                                {[['All', 'All'], ['Pending', 'Pending'], ['Received', 'Rececived'], ['Completed', 'Delivered'], ['Returned', 'Returned']].map(([value, label]) => (
                                    <div key={value} className="flex p-2 border-b mb-1">
                                        <input
                                            type="radio"
                                            checked={filters.status === value}
                                            onChange={() => updateFilter(filters, { status: value })}
                                            style={{ width: 20, height: 20 }}
                                            className="mr-3"
                                        /> {label}
                                    </div>
                                ))}
                            </div>
                            <div className="p-2">
                                {[['Today', 'Today'], ['Yesterday', 'Yestarday'], ['Weak', 'This Weak'], ['Month', 'This Monty'], ['between', 'Date Between'], ['any', 'Any Time']].map(([value, label]) => (
                                    <div key={value} className={`flex p-2 border-b mb-1 ${value === 'any' ? 'bg-gray-100' : ''}`}>
                                        <input
                                            type="radio"
                                            checked={filters.created_at === value}
                                            onChange={() => updateFilter(filters, { created_at: value })}
                                            style={{ width: 20, height: 20 }}
                                            className="mr-3"
                                        /> {label}
                                    </div>
                                ))}
                                {filters.created_at === "between" ? (
                                    <div>
                                        <hr />
                                        <div className="mb-1">
                                            <p>From </p>
                                            <input type="date" name="start_time" value={filters.start_time ?? ""} onChange={(e) => updateFilter(filters, { start_time: e.target.value })} id="start_time" />
                                        </div>
                                        <div className="mb-1">
                                            <p>to </p>
                                            <input type="date" name="end_time" value={filters.end_time ?? ""} onChange={(e) => updateFilter(filters, { end_time: e.target.value })} id="end_time" />
                                        </div>
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    </div>
                </Modal>
            </Container>
        </div>
    );
}