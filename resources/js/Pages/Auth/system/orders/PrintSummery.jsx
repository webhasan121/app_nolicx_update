import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import OrderStatus from "../../../../components/dashboard/OrderStatus";
import Container from "../../../../components/dashboard/Container";
import PrintLayout from "../../../../Layouts/Print";

export default function PrintSummery({ filters, orders = [], summary }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Orders Print Summery">
            <Head title="Orders Print Summery" />

            <Container>
                <div className="mb-2 text-center">
                    <h1>
                        <ApplicationName />
                    </h1>
                    <p className="">
                        Order Summery form {filters?.sd_formatted ?? ""} to {filters?.ed_formatted ?? ""}
                    </p>
                </div>
                <div>
                    <table className="border w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Buyer</th>
                                <th>Flow</th>
                                <th>Seller</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Comission</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            {orders.map((item, index) => (
                                <tr key={item.id} className="mb-3">
                                    <td>{index + 1}</td>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>
                                        <p className="text-xs">
                                            {item.user.name}
                                        </p>
                                        <p className="text-xs">
                                            {item.user.phone}| {item.user.email}
                                        </p>
                                    </td>
                                    <td>
                                        <div className="flex items-center text-xs">
                                            <div>
                                                <span className="text-xs"></span>
                                                {item.user_type}
                                            </div>
                                            <i className="fas fa-caret-right px-2"></i>
                                            {item.belongs_to_type}
                                        </div>
                                    </td>
                                    <td>
                                        <p className="text-xs">
                                            {item.seller.name}
                                            {" "}
                                            {item.seller.phone} | {item.seller.email}
                                        </p>
                                    </td>
                                    <td className="text-xs">
                                        <OrderStatus status={item.status} />
                                    </td>
                                    <td>{item.total ?? 0} TK</td>
                                    <td>{item.comission ?? 0} TK</td>
                                    <td>{item.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colSpan="6">{summary?.count ?? 0} Item</td>
                                <td>{summary?.sum_total ?? 0}</td>
                                <td>{summary?.sum_comission ?? 0}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </Container>
        </PrintLayout>
    );
}
