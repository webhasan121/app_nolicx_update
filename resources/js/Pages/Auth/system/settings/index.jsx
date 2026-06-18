import { Head, router, useForm } from "@inertiajs/react";
import { useState } from "react";
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

function CurrencyCard({ form, currencies = [], defaultCurrency = {} }) {
    const { t } = useTranslation();
    const [editingCode, setEditingCode] = useState(null);

    const clearForm = () => {
        setEditingCode(null);
        form.reset("code", "name", "symbol");
        form.clearErrors();
    };

    const save = (e) => {
        e.preventDefault();
        const url = editingCode
            ? `/dashboard/system/settings/currency/${encodeURIComponent(editingCode)}`
            : "/dashboard/system/settings/currency";

        form.post(url, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: clearForm,
        });
    };

    const editCurrency = (currency) => {
        setEditingCode(currency.code);
        form.clearErrors();
        form.setData({
            code: currency.code,
            name: currency.name,
            symbol: currency.symbol,
        });
    };

    const deleteCurrency = (currency) => {
        if (currency.code === defaultCurrency?.code) {
            return;
        }

        if (!window.confirm(t("Delete this currency?"))) {
            return;
        }

        router.delete(`/dashboard/system/settings/currency/${encodeURIComponent(currency.code)}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (editingCode === currency.code) {
                    clearForm();
                }
            },
        });
    };

    const setDefault = (code) => {
        router.post(
            "/dashboard/system/settings/default-currency",
            { code },
            {
                preserveScroll: true,
                preserveState: true,
            }
        );
    };

    return (
        <Section>
            <SectionHeader
                title={t("Currency")}
                content={t("Add currencies and select the default currency shown across the system.")}
            />

            <SectionInner>
                <div data-no-currency-sync>
                <div className="grid gap-4 lg:grid-cols-[280px_1fr]">
                    <form onSubmit={save} className="space-y-3 rounded-md border border-dashed p-4">
                        <div className="text-sm font-semibold">
                            {editingCode ? t("Edit Currency") : t("Add Currency")}
                        </div>
                        <div>
                            <label>{t("Code")} *</label>
                            <input
                                type="text"
                                className="w-full p-2 uppercase border rounded-md"
                                placeholder="BDT"
                                value={form.data.code}
                                onChange={(e) => form.setData("code", e.target.value.toUpperCase())}
                            />
                            <InputError messages={form.errors.code} className="mt-1" />
                        </div>
                        <div>
                            <label>{t("Code Name")} *</label>
                            <input
                                type="text"
                                className="w-full p-2 border rounded-md"
                                placeholder="Bangladeshi Taka"
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                            />
                            <InputError messages={form.errors.name} className="mt-1" />
                        </div>
                        <div>
                            <label>{t("Symbol")} *</label>
                            <input
                                type="text"
                                className="w-full p-2 border rounded-md"
                                placeholder="TK"
                                value={form.data.symbol}
                                onChange={(e) => form.setData("symbol", e.target.value)}
                            />
                            <InputError messages={form.errors.symbol} className="mt-1" />
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <PrimaryButton type="submit" disabled={form.processing}>
                                <i className="mr-2 fas fa-check"></i>
                                {form.processing ? t("Saving...") : editingCode ? t("Update") : t("Add")}
                            </PrimaryButton>
                            {editingCode ? (
                                <button
                                    type="button"
                                    onClick={clearForm}
                                    className="px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    {t("Cancel")}
                                </button>
                            ) : null}
                        </div>
                    </form>

                    <div className="grid gap-3 md:grid-cols-2">
                        {currencies.map((currency) => {
                            const isDefault = currency.code === defaultCurrency?.code;

                            return (
                                <div
                                    key={currency.code}
                                    className={`rounded-md border p-4 ${isDefault ? "border-green-500 bg-green-50" : "border-gray-200 bg-white"}`}
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <div>
                                            <div className="font-bold">{currency.code}</div>
                                            <div className="text-sm text-gray-600">{currency.name}</div>
                                            <div className="text-sm">{currency.symbol}</div>
                                        </div>
                                        <div className="flex flex-wrap justify-end gap-2">
                                            <button
                                                type="button"
                                                className={`rounded px-3 py-2 text-sm ${isDefault ? "bg-green-600 text-white" : "bg-indigo-600 text-white"}`}
                                                onClick={() => setDefault(currency.code)}
                                                disabled={isDefault}
                                            >
                                                {isDefault ? t("Default") : t("Set Default")}
                                            </button>
                                            <button
                                                type="button"
                                                className="inline-flex items-center justify-center w-9 h-9 text-xs text-white transition bg-blue-600 rounded hover:bg-blue-700"
                                                title={t("Edit")}
                                                onClick={() => editCurrency(currency)}
                                            >
                                                <i className="fas fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                className={`inline-flex items-center justify-center w-9 h-9 text-xs text-white transition rounded ${
                                                    isDefault ? "bg-gray-300 cursor-not-allowed" : "bg-red-600 hover:bg-red-700"
                                                }`}
                                                title={isDefault ? t("Default currency cannot be deleted.") : t("Delete")}
                                                onClick={() => deleteCurrency(currency)}
                                                disabled={isDefault}
                                            >
                                                <i className="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
                </div>
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
    const currencyForm = useForm({
        code: "",
        name: "",
        symbol: "",
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
                <CurrencyCard
                    form={currencyForm}
                    currencies={settings?.currencies ?? []}
                    defaultCurrency={settings?.default_currency ?? {}}
                />

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
