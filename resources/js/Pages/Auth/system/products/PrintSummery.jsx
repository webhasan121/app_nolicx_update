import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { products = [], filters = {} } = usePage().props;

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
        <PrintLayout title="Product Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Product Summary</p>
                        <p>
                            Status: {filters?.filter ?? "Active"} | Source:{" "}
                            {filters?.from ?? "all"}
                        </p>
                        {filters?.sd || filters?.ed ? (
                            <p>
                                {filters?.sd ? `From ${formatDate(filters.sd)}` : ""}
                                {filters?.sd && filters?.ed ? " " : ""}
                                {filters?.ed ? `To ${formatDate(filters.ed)}` : ""}
                            </p>
                        ) : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={products}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Owner</th>
                                <th>Price</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            {products.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        {item.name}
                                        <br />
                                        <span className="text-xs">{item.status}</span>
                                    </td>
                                    <td>
                                        {item.owner_name}
                                        <br />
                                        <span className="text-xs">
                                            {item.belongs_to_type}
                                        </span>
                                    </td>
                                    <td>{item.price ?? 0} TK</td>
                                    <td>{item.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="6">Total {products.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
