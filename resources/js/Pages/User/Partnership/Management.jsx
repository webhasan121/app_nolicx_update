import { useForm, usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import UserDash from "../../../components/user/dash/UserDash";

export default function Management() {
    const { name, email, phone, hasApplied, managementRequest } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        message: "",
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("user.management.apply"));
    };

    return (
        <UserDash>
            <Container>
                <div className="max-w-3xl p-6 mx-auto bg-white rounded-xl shadow">
                    {hasApplied ? (
                        <div className="py-10 text-center">
                            <h2 className="mb-2 text-2xl font-bold text-gray-800">
                                Management Application Submitted
                            </h2>

                            <p className="text-gray-600">
                                You have already applied for management access.
                            </p>

                            <div className="mt-4">
                                <span className="px-4 py-2 text-yellow-800 bg-yellow-100 rounded">
                                    Status: {managementRequest?.status ?? "pending"}
                                </span>
                            </div>

                            {managementRequest?.status === "approved" && (
                                <div className="mt-6 font-semibold text-green-700">
                                    Your management access has been approved!
                                </div>
                            )}
                            {managementRequest?.status === "rejected" && (
                                <div className="mt-6 text-red-600">
                                    Unfortunately your application was rejected.
                                </div>
                            )}
                        </div>
                    ) : (
                        <>
                            <h2 className="mb-6 text-2xl font-bold text-gray-800">
                                Management Partnership Form
                            </h2>

                            <form onSubmit={submit} className="space-y-5">
                                <div>
                                    <label className="block mb-1 text-sm font-semibold">
                                        Full Name
                                    </label>
                                    <input
                                        type="text"
                                        value={name || ""}
                                        className="w-full px-4 py-2 border rounded"
                                        readOnly
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-5">
                                    <div>
                                        <label className="block mb-1 text-sm font-semibold">
                                            Email
                                        </label>
                                        <input
                                            type="email"
                                            value={email || ""}
                                            className="w-full px-4 py-2 border rounded"
                                            readOnly
                                        />
                                    </div>

                                    <div>
                                        <label className="block mb-1 text-sm font-semibold">
                                            Phone
                                        </label>
                                        <input
                                            type="text"
                                            value={phone || ""}
                                            className="w-full px-4 py-2 border rounded"
                                            readOnly
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block mb-1 text-sm font-semibold">
                                        Message
                                    </label>
                                    <textarea
                                        rows="4"
                                        value={data.message}
                                        onChange={(e) => setData("message", e.target.value)}
                                        className="w-full px-4 py-2 border rounded"
                                    />
                                    {errors.message && (
                                        <div className="text-sm text-red-600">
                                            {errors.message}
                                        </div>
                                    )}
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-6 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    Submit Application
                                </button>
                            </form>
                        </>
                    )}
                </div>
            </Container>
        </UserDash>
    );
}

