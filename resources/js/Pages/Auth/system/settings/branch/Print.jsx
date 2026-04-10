import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../../Layouts/Print";
import ApplicationName from "../../../../../components/ApplicationName";
import Container from "../../../../../components/dashboard/Container";
import Table from "../../../../../components/dashboard/table/Table";

export default function Print() {
    const { branches = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Branch Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Branch Summary</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={branches}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            {branches.map((branch) => (
                                <tr key={branch.id}>
                                    <td>{branch.sl}</td>
                                    <td>{branch.name}</td>
                                    <td>{branch.email}</td>
                                    <td>{branch.type}</td>
                                    <td>{branch.created_at}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="5">Total {branches.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
