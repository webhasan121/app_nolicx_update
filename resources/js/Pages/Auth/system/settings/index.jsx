import { Head, router, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import InputError from "../../../../components/InputError";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Container from "../../../../components/dashboard/Container";
import useTranslation from "../../../../hooks/useTranslation";

function SettingCard({ title, content, href, buttonText, isQueueRunning, onStartQueue, queueControlAvailable, queueCommand }) {
    const { t } = useTranslation();

    return (
        <Section>
            <SectionHeader
                title={
                    <div className="flex items-center justify-between">
                        <div>{title}</div>
                    </div>
                }
                content={<span>{content}</span>}
            />

            <SectionInner>
                {isQueueRunning !== undefined ? (
                    isQueueRunning ? (
                        <div className="text-green-500">{t("Queue is running.")}</div>
                    ) : queueControlAvailable === false ? (
                        <div className="text-sm">
                            <div className="mb-2 text-gray-600">{t("Run manually:")}</div>
                            <code className="block p-2 normal-case bg-gray-100 border rounded">
                                {queueCommand}
                            </code>
                        </div>
                    ) : (
                        <PrimaryButton type="button" onClick={onStartQueue}>
                            <span>{t("Start Queue")}</span>
                        </PrimaryButton>
                    )
                ) : (
                    <NavLinkBtn href={href} className="">
                        <span>{buttonText}</span>
                    </NavLinkBtn>
                )}
            </SectionInner>
        </Section>
    );
}

function EnvCard({ title, content, label, form, field, type = "text", routeName }) {
    const save = (e) => {
        e.preventDefault();
        form.post(route(routeName), {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <Section>
            <SectionHeader
                title={
                    <div className="flex items-center justify-between">
                        <div>{title}</div>
                    </div>
                }
                content={<span>{content}</span>}
            />

            <SectionInner>
                <form onSubmit={save} className="relative">
                    <label>{label}</label>
                    <div className="flex items-center justify-between gap-4">
                        <input
                            type={type}
                            value={form.data[field]}
                            onChange={(e) => form.setData(field, e.target.value)}
                            className="w-full p-2 border rounded-md"
                        />
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="px-4 py-2 text-white bg-green-500 rounded-md disabled:opacity-50"
                        >
                            <span>{form.processing ? "Saving..." : "Save"}</span>
                        </button>
                    </div>
                    <InputError messages={form.errors[field]} className="mt-2" />
                </form>
            </SectionInner>
        </Section>
    );
}

function DepositPayNumbersCard({ form }) {
    const { t } = useTranslation();
    const rows = form.data.deposit_pay_numbers?.length
        ? form.data.deposit_pay_numbers
        : [{ name: "", value: "" }];

    const updateRow = (index, field, value) => {
        form.setData(
            "deposit_pay_numbers",
            rows.map((row, rowIndex) =>
                rowIndex === index ? { ...row, [field]: value } : row
            )
        );
    };

    const addRow = () => {
        form.setData("deposit_pay_numbers", [
            ...rows,
            { name: "", value: "" },
        ]);
    };

    const removeRow = (index) => {
        const nextRows = rows.filter((_, rowIndex) => rowIndex !== index);
        form.setData(
            "deposit_pay_numbers",
            nextRows.length ? nextRows : [{ name: "", value: "" }]
        );
    };

    const save = (e) => {
        e.preventDefault();
        form.post("/dashboard/system/settings/deposit-pay-numbers", {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <Section>
            <SectionHeader
                title={t("Deposit Payment Numbers")}
                content={t("Add the mobile bank or bank account numbers shown on the user deposit page.")}
            />

            <SectionInner>
                <form onSubmit={save}>
                    <div className="space-y-2">
                        {rows.map((row, index) => (
                            <div
                                key={index}
                                className="flex flex-wrap items-center gap-2"
                            >
                                <input
                                    type="text"
                                    className="w-full p-2 border rounded-md sm:w-52"
                                    placeholder={t("Name")}
                                    value={row.name}
                                    onChange={(e) =>
                                        updateRow(index, "name", e.target.value)
                                    }
                                />
                                <input
                                    type="text"
                                    className="w-full p-2 border rounded-md sm:w-56"
                                    placeholder={t("Value")}
                                    value={row.value}
                                    onChange={(e) =>
                                        updateRow(index, "value", e.target.value)
                                    }
                                />
                                <button
                                    type="button"
                                    className="inline-flex items-center justify-center w-8 h-8 text-gray-600 border rounded hover:bg-gray-100"
                                    onClick={() => removeRow(index)}
                                    aria-label={t("Remove")}
                                >
                                    <i className="fas fa-minus"></i>
                                </button>
                            </div>
                        ))}
                    </div>

                    <div className="flex flex-wrap items-center gap-2 mt-2">
                        <button
                            type="button"
                            className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                            onClick={addRow}
                        >
                            <i className="mr-2 fas fa-plus"></i>
                            {t("Add Attribute")}
                        </button>
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="px-4 py-2 text-white bg-green-500 rounded-md disabled:opacity-50"
                        >
                            {form.processing ? t("Saving...") : t("Save")}
                        </button>
                    </div>
                    <InputError
                        messages={
                            form.errors.deposit_pay_numbers ||
                            form.errors["deposit_pay_numbers.0.name"] ||
                            form.errors["deposit_pay_numbers.0.value"]
                        }
                        className="mt-2"
                    />
                </form>
            </SectionInner>
        </Section>
    );
}

export default function Index({ settings }) {
    const { t } = useTranslation();
    const supportMailForm = useForm({
        support_mail: settings?.support_mail ?? "",
    });

    const whatsappForm = useForm({
        whatsapp_no: settings?.whatsapp_no ?? "",
    });

    const dbidForm = useForm({
        dbid_no: settings?.dbid_no ?? "",
    });

    const tradeLicenseForm = useForm({
        trade_license: settings?.trade_license ?? "",
    });

    const playstoreForm = useForm({
        playstore_link: settings?.playstore_link ?? "",
    });
    const depositPayNumbersForm = useForm({
        deposit_pay_numbers: settings?.deposit_pay_numbers?.length
            ? settings.deposit_pay_numbers
            : [{ name: "", value: "" }],
    });

    const developerPercentageForm = useForm({
        developer_percentage: settings?.developer_percentage ?? "",
    });
    const managementPercentageForm = useForm({
        management_percentage: settings?.management_percentage ?? "",
    });
    const managementTeamPercentageForm = useForm({
        management_team_percentage: settings?.management_team_percentage ?? "",
    });

    const startQueue = () => {
        router.post(route("system.settings.queue.start"));
    };

    return (
        <AppLayout title={t("Settings")} header={<PageHeader>{t("Settings")}</PageHeader>}>
            <Head title={t("Settings")} />

            <Container>
                <section className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <SettingCard
                        title={t("Page Setup")}
                        content={t("Setup your necessary pages from here. add, edit and delete.")}
                        href={route("system.pages.index")}
                        buttonText="Go To Page Setup"
                    />

                    <SettingCard
                        title={t("Branch Management")}
                        content={t("Setup your necessary branches from here. add, edit and delete.")}
                        href={route("system.branches.index")}
                        buttonText="Manage Branch"
                    />

                    <SettingCard
                        title={t("Queue Setup")}
                        content={t("Start your queue for your system. This will help you to manage your queue system.")}
                        isQueueRunning={settings?.isQueueRunning}
                        onStartQueue={startQueue}
                        queueControlAvailable={settings?.queueControlAvailable}
                        queueCommand={settings?.queueCommand}
                    />

                    <SettingCard
                        title={t("Geolocation Setup")}
                        content={t("Setup your rider targeted area from here. also edit and delete your gelolocation names. Country, State and City.")}
                        href={route("system.geolocations.index")}
                        buttonText="Go To Setup"
                    />
                </section>

                <DepositPayNumbersCard form={depositPayNumbersForm} />

                <section className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <EnvCard
                        title={t("Support Email")}
                        content={t("Update support email from here")}
                        label={t("Support Email")}
                        form={supportMailForm}
                        field="support_mail"
                        type="email"
                        routeName="system.settings.support-email.update"
                    />

                    <EnvCard
                        title={t("WhatsApp")}
                        content={t("Update whatsapp number from here")}
                        label={t("WhatsApp Number")}
                        form={whatsappForm}
                        field="whatsapp_no"
                        routeName="system.settings.whatsapp.update"
                    />

                    <EnvCard
                        title={t("DBID No.")}
                        content={t("Update DBID no. from here")}
                        label={t("DBID No.")}
                        form={dbidForm}
                        field="dbid_no"
                        routeName="system.settings.dbid.update"
                    />

                    <EnvCard
                        title={t("Trade License No.")}
                        content={t("Update trade license from here")}
                        label={t("Trade License No.")}
                        form={tradeLicenseForm}
                        field="trade_license"
                        routeName="system.settings.trade-license.update"
                    />

                    <EnvCard
                        title={t("Playstore")}
                        content={t("Update playstore app url from here")}
                        label={t("Playstore App URL")}
                        form={playstoreForm}
                        field="playstore_link"
                        type="url"
                        routeName="system.settings.playstore.update"
                    />
                    <EnvCard
                        title={t("Developer Percentage")}
                        content={t("Update developer percentage from here")}
                        label={t("Developer Percentage")}
                        form={developerPercentageForm}
                        field="developer_percentage"
                        type="number"
                        routeName="system.settings.developer-percentage.update"
                    />
                    <EnvCard
                        title={t("Management Percentage")}
                        content={t("Update management percentage from here")}
                        label={t("Management Percentage")}
                        form={managementPercentageForm}
                        field="management_percentage"
                        type="number"
                        routeName="system.settings.management-percentage.update"
                    />
                    <EnvCard
                        title={t("Management TM Percentage")}
                        content={t("Update management TM percentage from here")}
                        label={t("Management TM Percentage")}
                        form={managementTeamPercentageForm}
                        field="management_team_percentage"
                        type="number"
                        routeName="system.settings.management-team-percentage.update"
                    />
                </section>
            </Container>
        </AppLayout>
    );
}
