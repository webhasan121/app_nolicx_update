import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { users = [], sd, ed } = usePage().props;

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
        <PrintLayout title="Users Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>
                            {" "}
                            Users Summery form {formatDate(sd)} to {formatDate(ed)}{" "}
                        </p>
                    </div>
                    <hr className="my-2" />

                    <Table data={users}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Ref &amp; Reference</th>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th>VIP</th>
                                <th>Order</th>
                                <th>Wallet</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>
                            {users.map((user, index) => (
                                <tr key={user.id}>
                                    <td>{index + 1}</td>
                                    <td>{user.id ?? "N/A"}</td>
                                    <td>
                                        {user.name ?? "N/A"}
                                        <br />
                                        <b className="text-xs">{user.email ?? "N/A"}</b>
                                    </td>
                                    <td>
                                        {user.ref ?? "N/A"}
                                        <br />
                                        <span className="px-2 text-xs rounded border">
                                            {user.reference ?? "Not Found"} &gt;{" "}
                                            {user.reference_owner_name}
                                        </span>
                                    </td>
                                    <td>
                                        <div className="flex">
                                            {user.roles.map((role) => (
                                                <div
                                                    key={`${user.id}-${role}`}
                                                    className="px-1 rounded border m-1 text-sm"
                                                >
                                                    {role}
                                                </div>
                                            ))}
                                        </div>
                                    </td>
                                    <td>{user.permissions_count ?? "Not Found !"}</td>
                                    <td>
                                        <div className={user.vip_status.className}>
                                            {user.vip_status.label}
                                        </div>
                                    </td>
                                    <td>{user.orders_count ?? "0"}</td>
                                    <td>{user.coin ?? "0"}</td>
                                    <td>{user.created_at_formatted ?? ""}</td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colSpan="11">Total {users.length} Items </td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
