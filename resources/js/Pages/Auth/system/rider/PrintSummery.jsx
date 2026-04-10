import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { riders = [], filters = {} } = usePage().props;

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

        return date.toLocaleDateString("en-GB");
    };

    return (
        <PrintLayout title="Rider Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Rider Summary</p>
                        {filters?.condition && filters.condition !== "all" ? (
                            <p>Status: {filters.condition}</p>
                        ) : null}
                        {filters?.sd || filters?.ed ? (
                            <p>
                                {filters?.sd ? `From ${formatDate(filters.sd)}` : ""}
                                {filters?.sd && filters?.ed ? " " : ""}
                                {filters?.ed ? `To ${formatDate(filters.ed)}` : ""}
                            </p>
                        ) : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={riders}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Join Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {riders.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.sl}</td>
                                    <td>{item.user_name}</td>
                                    <td>{item.status}</td>
                                    <td>{item.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="4">Total {riders.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
