import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function Print() {
    const { levels = [], search = "" } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Levels Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Levels Summary{search ? ` for "${search}"` : ""}</p>
                    </div>
                    <hr className="my-2" />

                    <Table data={levels}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Level Name</th>
                                <th>Normal Users</th>
                                <th>VIP Users</th>
                                <th>Commission</th>
                                <th>Reward</th>
                            </tr>
                        </thead>

                        <tbody>
                            {levels.map((level) => (
                                <tr key={level.id}>
                                    <td>{level.sl}.</td>
                                    <td>{level.name ?? "N/A"}</td>
                                    <td>{Number(level.req_users ?? 0).toLocaleString()}</td>
                                    <td>{Number(level.vip_users ?? 0).toLocaleString()}</td>
                                    <td>{Number(level.bonus ?? 0).toFixed(2)}</td>
                                    <td>{level.rewards || "Not Available"}</td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colSpan="6">Total {levels.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
