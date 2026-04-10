import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { vendors = [], filters = {} } = usePage().props;

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
        <PrintLayout title="Vendor Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Vendor Summary</p>
                        {filters?.filter && filters.filter !== "*" ? (
                            <p>Status: {filters.filter}</p>
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

                    <Table data={vendors}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Commission</th>
                                <th>Product</th>
                                <th>Join</th>
                            </tr>
                        </thead>

                        <tbody>
                            {vendors.map((vendor, index) => (
                                <tr key={vendor.id}>
                                    <td>{index + 1}</td>
                                    <td>{vendor.id}</td>
                                    <td>
                                        {vendor.user_name}
                                        <br />
                                        <span className="text-xs">
                                            {vendor.shop_name_en}
                                        </span>
                                    </td>
                                    <td>
                                        {vendor.email}
                                        <br />
                                        <span className="text-xs">
                                            {vendor.phone}
                                        </span>
                                        <br />
                                        <span className="text-xs">
                                            {vendor.location}
                                        </span>
                                    </td>
                                    <td>{vendor.status}</td>
                                    <td>{vendor.system_get_comission}%</td>
                                    <td>{vendor.products_count}</td>
                                    <td>{vendor.created_at_formatted}</td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colSpan="8">Total {vendors.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
