import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionInner from "../../../../components/dashboard/section/Inner";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index({ filters }) {
    const { t } = useTranslation();
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
            title={t("Generate Reports")}
            header={<PageHeader>{t("Generate Reports")}</PageHeader>}
        >
            <Head title={t("Generate Reports")} />

            <div className="flex justify-center items-center w-full">
                <div style={{ width: "350px" }} className="border rounded-md p-4 bg-white">
                    <SectionInner>
                        <form onSubmit={submit}>
                            <div className="mb-2">
                                <p>{t("Report For")}</p>
                                <select
                                    value={form.data.nav}
                                    onChange={(e) => form.setData("nav", e.target.value)}
                                    className="w-full rounded-md"
                                >
                                    <option value="">{t("-- Select --")}</option>
                                    <option value="Deposit">{t("Deposit")}</option>
                                    <option value="Withdraw">{t("Withdraw")}</option>
                                    <option value="Sells">{t("Sells")}</option>
                                    <option value="Vip">{t("Vip")}</option>
                                    <option value="Product">{t("Products")}</option>
                                </select>
                            </div>

                            <div className="mb-2">
                                <p>{t("From")}</p>
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
                                <p>{t("To")}</p>
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
                                <p>{t("ID")}</p>
                                <input
                                    type="text"
                                    value={form.data.sid}
                                    onChange={(e) => form.setData("sid", e.target.value)}
                                    placeholder={t("Optional")}
                                    className="w-full rounded-md"
                                />
                            </div>

                            <div className="w-ful text-end">
                                <PrimaryButton type="submit">{t("Generate")}</PrimaryButton>
                            </div>
                        </form>
                    </SectionInner>
                </div>
            </div>
        </AppLayout>
    );
}
