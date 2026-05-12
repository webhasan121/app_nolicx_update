import { usePage } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import PrintLayout from "../../../../Layouts/Print";

export default function ManagementTeamPrint() {
    const { applications = [], filters = {} } = usePage().props;

    useEffect(() => {
        setTimeout(() => window.print(), 300);
    }, []);

    return (
        <PrintLayout title="Management TM Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Management TM Summary</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />
                    <table className="table">
                        <thead>
                            <tr>
                                <th>SL No.</th>
                                <th>Name of User</th>
                                <th>User Email</th>
                                <th>Status</th>
                                <th>Responded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            {applications.map((app) => (
                                <tr key={app.id}>
                                    <td>{app.sl}</td>
                                    <td>{app.user_name}</td>
                                    <td>{app.user_email}</td>
                                    <td>{app.status_text}</td>
                                    <td>{app.responder_name}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </Container>
            </div>
        </PrintLayout>
    );
}
