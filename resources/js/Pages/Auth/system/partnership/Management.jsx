import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";

export default function Management({ applications = { data: [] } }) {
    const items = applications?.data ?? [];

    const accept = (id) => {
        router.post(route("system.partnership.management.accept", { id }));
    };

    const reject = (id) => {
        router.post(route("system.partnership.management.reject", { id }));
    };

    const destroy = (id) => {
        if (!window.confirm("Are you sure you want to delete this application?")) {
            return;
        }

        router.delete(route("system.partnership.management.destroy", { id }));
    };

    return (
        <AppLayout
            title="Partnership - Management Access"
            header={<PageHeader>Partnership - Management Access</PageHeader>}
        >
            <Head title="Partnership - Management Access" />

            <Container>
                <Section>
                    <SectionHeader title="Management Access" content="" />

                    <div className="py-3">
                        <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                            <table className="min-w-full divide-y divide-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">SL No.</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Name of User</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">User Email</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Responded By</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600" width="100">A/C</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {items.map((app) => (
                                        <tr key={app.id} className="hover:bg-gray-50 transition">
                                            <td className="px-4 py-3 font-medium text-gray-700">{app.sl}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.user_name}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.user_email}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.status_text}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.responder_name}</td>
                                            <td className="px-4 py-3 text-center space-x-2">
                                                {app.status !== null ? (
                                                    <>
                                                        {app.status === 1 ? (
                                                            <button className="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition" disabled>
                                                                <i className="fas fa-check-circle"></i>
                                                            </button>
                                                        ) : (
                                                            <button className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition" disabled>
                                                                <i className="fas fa-circle-xmark"></i>
                                                            </button>
                                                        )}
                                                        <button
                                                            type="button"
                                                            onClick={() => destroy(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-trash-alt"></i>
                                                        </button>
                                                    </>
                                                ) : (
                                                    <>
                                                        <button
                                                            type="button"
                                                            onClick={() => accept(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-check"></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => reject(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-times"></i>
                                                        </button>
                                                    </>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Section>
            </Container>
        </AppLayout>
    );
}
