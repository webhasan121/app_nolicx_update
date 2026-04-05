import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { vip = [], sdate, edate, totals = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    const formatDate = (value) => {
        if (!value) {
            return "";
        }

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        const dd = String(date.getDate()).padStart(2, "0");
        const mm = String(date.getMonth() + 1).padStart(2, "0");
        const yyyy = date.getFullYear();

        return `${yyyy}-${dd}-${mm}`;
    };

    return (
        <PrintLayout title="VIP Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>
                            User Summery{" "}
                            {sdate && edate ? (
                                <>
                                    From {formatDate(sdate)} To {formatDate(edate)}
                                </>
                            ) : null}
                        </p>
                    </div>
                    <hr className="my-2" />

                    <div>
                        <Table data={vip}>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Comission</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Validity</th>
                                </tr>
                            </thead>

                            <tbody>
                                {vip.map((item) => (
                                    <tr key={`${item.sl}-${item.name}`}>
                                        <td>{item.sl}</td>
                                        <td>
                                            {item.name}
                                            <br />
                                            <div className="text-xs">
                                                {item.user_email}
                                            </div>
                                        </td>
                                        <td>
                                            {item.package_name}
                                            <div className="text-xs">
                                                {" "}
                                                {item.task_type}{" "}
                                            </div>
                                        </td>
                                        <td>{item.amount}</td>
                                        <td>{item.comission}</td>
                                        <td>
                                            {item.status}
                                            <br />
                                            {item.deleted_at_formatted ? (
                                                <span className="text-xs text-red-900 text-bold ">
                                                    {item.deleted_at_formatted}
                                                </span>
                                            ) : null}
                                        </td>
                                        <td>
                                            <div className="text-nowrap">
                                                {item.created_at_formatted}
                                            </div>
                                        </td>
                                        <td>
                                            {item.valid_till_formatted}
                                            <div className="text-xs">
                                                {item.valid_till_human}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colSpan="3">{totals.count ?? vip.length} Items </td>
                                    <td className="font-bold">
                                        {totals.package_price ?? 0}
                                    </td>
                                    <td className="font-bold">
                                        {totals.comission ?? 0}
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </Table>
                    </div>
                </Container>
            </div>
        </PrintLayout>
    );
}
