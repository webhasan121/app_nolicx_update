import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function HistoryPrint() {
    const { histories = [], search = "" } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Level-Up History Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Level-Up History Summary{search ? ` for "${search}"` : ""}</p>
                    </div>
                    <hr className="my-2" />

                    <Table data={histories}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name of Users</th>
                                <th>From Level</th>
                                <th>To Level</th>
                                <th>Level-Up At</th>
                            </tr>
                        </thead>

                        <tbody>
                            {histories.map((history) => (
                                <tr key={history.id}>
                                    <td>{history.sl}.</td>
                                    <td>{history.user_name || "N/A"}</td>
                                    <td>{history.from_level_name || "N/A"}</td>
                                    <td>{history.to_level_name || "N/A"}</td>
                                    <td>{history.created_at_formatted || "N/A"}</td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colSpan="5">Total {histories.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
