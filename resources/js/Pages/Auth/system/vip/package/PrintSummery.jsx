import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../../Layouts/Print";
import ApplicationName from "../../../../../components/ApplicationName";
import Container from "../../../../../components/dashboard/Container";
import Table from "../../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { packages = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Package Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>VIP Package Summary</p>
                        <p>Status: {filters?.nav ?? "Active"}</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={packages}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Timer</th>
                                <th>Coin</th>
                                <th>Sell</th>
                                <th>Earn</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            {packages.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.name}</td>
                                    <td>{item.price} TK</td>
                                    <td>{item.countdown} Minute</td>
                                    <td>
                                        D - {item.coin}
                                        <br />
                                        M - {item.m_coin}
                                        <br />
                                        Ref - {item.ref_owner_get_coin}
                                    </td>
                                    <td>{item.users_count ?? 0}</td>
                                    <td>{item.earn}</td>
                                    <td>{item.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="8">Total {packages.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
