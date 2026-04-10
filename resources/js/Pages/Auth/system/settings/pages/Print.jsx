import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../../Layouts/Print";
import ApplicationName from "../../../../../components/ApplicationName";
import Container from "../../../../../components/dashboard/Container";
import Table from "../../../../../components/dashboard/table/Table";

export default function Print() {
    const { pages = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Page Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Page Summary</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={pages}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Content</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {pages.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.id}</td>
                                    <td>
                                        {item.name}
                                        <br />
                                        <span className="text-xs">{item.title}</span>
                                    </td>
                                    <td>{item.content}</td>
                                    <td>{item.status}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="4">Total {pages.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
