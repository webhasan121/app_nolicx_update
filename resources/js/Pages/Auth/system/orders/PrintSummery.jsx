import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import OrderStatus from "../../../../components/dashboard/OrderStatus";
import Container from "../../../../components/dashboard/Container";
import PrintLayout from "../../../../Layouts/Print";

const ORDER_PRINT_STYLE = `
    #pdf-content .orders-print-table th {
        background: #f8fafc;
        border: 1px solid #d1d5db;
        font-weight: 700;
        text-align: left;
    }

    #pdf-content .orders-print-table td {
        border: 1px solid #d1d5db;
        line-height: 1.35;
    }

    #pdf-content .orders-print-table .text-center {
        text-align: center;
    }

    #pdf-content .orders-print-table .text-right {
        text-align: right;
    }

    #pdf-content .orders-print-table .contact-name {
        font-weight: 700;
        margin-bottom: 2px;
    }

    #pdf-content .orders-print-table .contact-line {
        color: #374151;
        font-size: 10px;
        word-break: break-all;
    }

    #pdf-content .orders-print-table .status-cell span {
        display: inline-block;
        border-radius: 4px;
        padding: 2px 5px;
        color: #111827 !important;
        background: #e5e7eb !important;
        font-size: 10px;
    }

    #pdf-content .orders-print-summary {
        margin-top: 10px;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    #pdf-content .orders-print-summary table {
        width: 100%;
        border-collapse: collapse;
    }

    #pdf-content .orders-print-summary td {
        border: 1px solid #d1d5db;
        background: #f8fafc;
        font-weight: 700;
        padding: 6px;
    }
`;

export default function PrintSummery({ filters, orders = [], summary }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Orders Print Summery">
            <Head title="Orders Print Summery">
                <style>{ORDER_PRINT_STYLE}</style>
            </Head>

            <div id="pdf-content">
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
                    <table className="orders-print-table w-full">
                        <colgroup>
                            <col style={{ width: "4%" }} />
                            <col style={{ width: "5%" }} />
                            <col style={{ width: "20%" }} />
                            <col style={{ width: "10%" }} />
                            <col style={{ width: "21%" }} />
                            <col style={{ width: "9%" }} />
                            <col style={{ width: "10%" }} />
                            <col style={{ width: "10%" }} />
                            <col style={{ width: "11%" }} />
                        </colgroup>
                        <thead>
                            <tr>
                                <th className="text-center">#</th>
                                <th className="text-center">ID</th>
                                <th>Buyer</th>
                                <th className="text-center">Flow</th>
                                <th>Seller</th>
                                <th className="text-center">Status</th>
                                <th className="text-right">Amount</th>
                                <th className="text-right">Comission</th>
                                <th className="text-center">Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            {orders.map((item, index) => (
                                <tr key={item.id}>
                                    <td className="text-center">{index + 1}</td>
                                    <td className="text-center">{item.id ?? "N/A"}</td>
                                    <td>
                                        <div className="contact-name">
                                            {item.user.name}
                                        </div>
                                        <div className="contact-line">{item.user.phone}</div>
                                        <div className="contact-line">{item.user.email}</div>
                                    </td>
                                    <td className="text-center">
                                        {item.user_type} &gt; {item.belongs_to_type}
                                    </td>
                                    <td>
                                        <div className="contact-name">
                                            {item.seller.name}
                                        </div>
                                        <div className="contact-line">{item.seller.phone}</div>
                                        <div className="contact-line">{item.seller.email}</div>
                                    </td>
                                    <td className="status-cell text-center">
                                        <OrderStatus status={item.status} />
                                    </td>
                                    <td className="text-right">{item.total ?? 0} TK</td>
                                    <td className="text-right">{item.comission ?? 0} TK</td>
                                    <td className="text-center">{item.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>

                    </table>

                    <div className="orders-print-summary">
                        <table>
                            <colgroup>
                                <col style={{ width: "68%" }} />
                                <col style={{ width: "16%" }} />
                                <col style={{ width: "16%" }} />
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td>{summary?.count ?? 0} Item</td>
                                    <td className="text-right">{summary?.sum_total ?? 0} TK</td>
                                    <td className="text-right">{summary?.sum_comission ?? 0} TK</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Container>
            </div>
        </PrintLayout>
    );
}
