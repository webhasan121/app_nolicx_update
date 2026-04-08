import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function Index({ filters }) {
    const form = useForm({
        nav: filters?.nav ?? "Deposit",
        sdate: filters?.sdate ?? "",
        edate: filters?.edate ?? "",
        sid: filters?.sid ?? "",
    });

    const submit = (e) => {
        e.preventDefault();

        const params = {
            nav: form.data.nav,
            sdate: form.data.sdate,
            edate: form.data.edate,
        };

        if (form.data.sid) {
            params.id = form.data.sid;
        }

        window.location.assign(route("system.report.generate", params));
    };

    return (
        <AppLayout
            title="Generate Reports"
            header={<PageHeader>Generate Reports</PageHeader>}
        >
            <Head title="Generate Reports" />

            <div className="flex justify-center items-center w-full">
                <div style={{ width: "350px" }} className="border rounded-md p-4 bg-white">
                    <SectionInner>
                        <form onSubmit={submit}>
                            <div className="mb-2">
                                <p>Report For</p>
                                <select
                                    value={form.data.nav}
                                    onChange={(e) => form.setData("nav", e.target.value)}
                                    className="w-full rounded-md"
                                >
                                    <option value=""> -- Select --</option>
                                    <option value="Deposit">Deposit</option>
                                    <option value="Withdraw">Withdraw</option>
                                    <option value="Sells">Sells</option>
                                    <option value="Vip">Vip</option>
                                    <option value="Product">Products</option>
                                </select>
                            </div>

                            <div className="mb-2">
                                <p>From</p>
                                <input
                                    type="date"
                                    value={form.data.sdate}
                                    onChange={(e) => form.setData("sdate", e.target.value)}
                                    className="w-full rounded-md"
                                />
                                {form.errors.sdate && (
                                    <strong className="text-red-900"> {form.errors.sdate} </strong>
                                )}
                            </div>

                            <div className="mb-2">
                                <p>To</p>
                                <input
                                    type="date"
                                    value={form.data.edate}
                                    onChange={(e) => form.setData("edate", e.target.value)}
                                    className="w-full rounded-md"
                                />
                                {form.errors.edate && (
                                    <strong className="text-red-900"> {form.errors.edate} </strong>
                                )}
                            </div>

                            <div className="mb-3">
                                <p>ID</p>
                                <input
                                    type="text"
                                    value={form.data.sid}
                                    onChange={(e) => form.setData("sid", e.target.value)}
                                    placeholder="Optional"
                                    className="w-full rounded-md"
                                />
                            </div>

                            <div className="w-ful text-end">
                                <PrimaryButton type="submit">
                                    Generate
                                </PrimaryButton>
                            </div>
                        </form>
                    </SectionInner>
                </div>
            </div>
        </AppLayout>
    );
}
