import { Head } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";

function StatCard({ label, value }) {
    return (
        <div className="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <div className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </div>
            <div className="mt-2 text-2xl font-bold text-indigo-900">
                {value ?? 0}
            </div>
        </div>
    );
}

function StatusBadge({ active }) {
    return (
        <span
            className={`inline-flex rounded-md px-2 py-1 text-xs font-semibold ${
                active
                    ? "bg-green-100 text-green-800"
                    : "bg-red-100 text-red-800"
            }`}
        >
            {active ? "Active" : "Inactive"}
        </span>
    );
}

function InfoRow({ label, value }) {
    return (
        <div className="border-b border-slate-100 py-3 last:border-b-0">
            <div className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </div>
            <div className="mt-1 text-sm font-medium text-slate-900">
                {value || "N/A"}
            </div>
        </div>
    );
}

function BadgeList({ items = [], empty = "None" }) {
    if (!items.length) {
        return <span className="text-sm text-slate-500">{empty}</span>;
    }

    return (
        <div className="flex flex-wrap gap-2">
            {items.map((item) => (
                <span
                    key={item}
                    className="rounded-md border border-indigo-100 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-800"
                >
                    {item}
                </span>
            ))}
        </div>
    );
}

function ShopList({ items = [], type }) {
    if (!items.length) {
        return <span className="text-sm text-slate-500">No request found</span>;
    }

    return (
        <div className="space-y-2">
            {items.map((item) => (
                <div
                    key={`${type}-${item.id}`}
                    className="rounded-md border border-slate-200 bg-slate-50 px-3 py-2"
                >
                    <div className="text-sm font-semibold text-slate-900">
                        {item.name ?? type}
                    </div>
                    <div className="mt-1 text-xs font-medium text-slate-600">
                        {item.status ?? "N/A"}
                        {item.area_condition ? ` - ${item.area_condition}` : ""}
                    </div>
                </div>
            ))}
        </div>
    );
}

function MiniTable({ title, rows = [], columns = [] }) {
    return (
        <Section>
            <SectionHeader title={title} content="" />
            <SectionInner>
                <Table data={rows}>
                    <thead>
                        <tr>
                            {columns.map((column) => (
                                <th key={column.key}>{column.label}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {rows.map((row) => (
                            <tr key={row.id}>
                                {columns.map((column) => (
                                    <td key={`${row.id}-${column.key}`}>
                                        {column.render
                                            ? column.render(row)
                                            : row[column.key] ?? "N/A"}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </Table>
            </SectionInner>
        </Section>
    );
}

export default function Details({ userDetails }) {
    const { t } = useTranslation();
    const user = userDetails ?? {};

    return (
        <AppLayout
            title={t("User Details")}
            header={<PageHeader>{t("User Details")}</PageHeader>}
        >
            <Head title={t("User Details")} />

            <Container>
                <Section className="border-l-4 border-l-orange-500">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div className="flex flex-wrap items-center gap-3">
                                <h1 className="text-2xl font-bold text-slate-950">
                                    {user.name}
                                </h1>
                                <StatusBadge active={user.is_active} />
                            </div>
                            <div className="mt-1 text-sm text-slate-600">
                                #{user.id} - {user.email}
                            </div>
                            <div className="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                                <span className="rounded-md bg-slate-100 px-2 py-1">
                                    Ref: {user.ref ?? "N/A"}
                                </span>
                                <span className="rounded-md bg-slate-100 px-2 py-1">
                                    Joined: {user.created_at_formatted ?? "N/A"}
                                </span>
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <NavLinkBtn href="/dashboard/system/users">
                                <i className="fas fa-arrow-left mr-2"></i>
                                {t("Back")}
                            </NavLinkBtn>
                            <NavLinkBtn
                                href={route("system.users.edit", { id: user.id })}
                            >
                                <i className="fas fa-pen mr-2"></i>
                                {t("Edit")}
                            </NavLinkBtn>
                        </div>
                    </div>
                </Section>

                <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
                    <StatCard label={t("Wallet")} value={user.coin} />
                    <StatCard label={t("Available")} value={user.available_coin} />
                    <StatCard label={t("Orders")} value={user.counts?.orders} />
                    <StatCard label={t("Reseller Orders")} value={user.counts?.reseller_orders} />
                    <StatCard label={t("Deposits")} value={user.counts?.deposits} />
                    <StatCard label={t("Withdraws")} value={user.counts?.withdraws} />
                </div>

                <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
                    <Section className="xl:col-span-2">
                        <SectionHeader
                            title={t("Profile")}
                            content=""
                        />
                        <SectionInner>
                            <div className="grid grid-cols-1 gap-x-8 md:grid-cols-2">
                                <InfoRow label={t("Name")} value={user.name} />
                                <InfoRow label={t("Email")} value={user.email} />
                                <InfoRow label={t("Phone")} value={user.phone} />
                                <InfoRow label={t("Gender")} value={user.gender} />
                                <InfoRow label={t("Date of Birth")} value={user.dob} />
                                <InfoRow label={t("KYC Status")} value={user.kyc_status} />
                                <InfoRow label={t("Location")} value={user.location} />
                                <InfoRow label={t("Language")} value={user.language} />
                                <InfoRow label={t("Created")} value={user.created_at_formatted} />
                                <InfoRow label={t("Email Verified")} value={user.email_verified_at} />
                            </div>
                        </SectionInner>
                    </Section>

                    <Section>
                        <SectionHeader
                            title={t("Access")}
                            content=""
                        />
                        <SectionInner>
                            <InfoRow label={t("Active Navigation")} value={user.active_nav} />
                            <InfoRow label={t("Level")} value={user.level} />
                            <div className="border-b border-slate-100 py-3">
                                <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t("Account Status")}
                                </div>
                                <StatusBadge active={user.is_active} />
                            </div>
                            <div className="py-3">
                                <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t("Roles")}
                                </div>
                                <BadgeList items={user.roles} />
                            </div>
                            <div className="py-3">
                                <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t("Direct Permissions")}
                                </div>
                                <BadgeList items={user.permissions} />
                            </div>
                            <div className="py-3">
                                <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t("Permissions Via Role")}
                                </div>
                                <BadgeList items={user.permissions_via_role} />
                            </div>
                        </SectionInner>
                    </Section>
                </div>

                <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
                    <Section>
                        <SectionHeader title={t("Referral")} content="" />
                        <SectionInner>
                            <InfoRow label={t("Own Ref")} value={user.ref} />
                            <InfoRow label={t("Used Reference")} value={user.reference} />
                            <InfoRow label={t("Reference Owner")} value={user.reference_owner_name} />
                        </SectionInner>
                    </Section>

                    <Section>
                        <SectionHeader title={t("VIP")} content="" />
                        <SectionInner>
                            <InfoRow label={t("Package")} value={user.vip?.package} />
                            <InfoRow label={t("Status")} value={user.vip?.status} />
                            <InfoRow label={t("Task Type")} value={user.vip?.task_type} />
                            <InfoRow label={t("Valid Till")} value={user.vip?.valid_till} />
                        </SectionInner>
                    </Section>

                    <Section>
                        <SectionHeader title={t("Shop Requests")} content="" />
                        <SectionInner>
                            <div className="space-y-4">
                                <div>
                                    <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        {t("Vendor")}
                                    </div>
                                    <ShopList items={user.shops?.vendor} type="Vendor" />
                                </div>
                                <div>
                                    <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        {t("Reseller")}
                                    </div>
                                    <ShopList items={user.shops?.reseller} type="Reseller" />
                                </div>
                                <div>
                                    <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        {t("Rider")}
                                    </div>
                                    <ShopList items={user.shops?.rider} type="Rider" />
                                </div>
                            </div>
                        </SectionInner>
                    </Section>
                </div>

                <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
                    <MiniTable
                        title={t("Recent Orders")}
                        rows={user.recent_orders ?? []}
                        columns={[
                            { key: "id", label: "ID" },
                            { key: "status", label: "Status" },
                            { key: "total", label: "Total" },
                            { key: "created_at", label: "Date" },
                        ]}
                    />
                    <MiniTable
                        title={t("Recent Deposits")}
                        rows={user.recent_deposits ?? []}
                        columns={[
                            { key: "id", label: "ID" },
                            { key: "amount", label: "Amount" },
                            { key: "method", label: "Method" },
                            {
                                key: "confirmed",
                                label: "Status",
                                render: (row) => (row.confirmed ? "Confirmed" : "Pending"),
                            },
                        ]}
                    />
                    <MiniTable
                        title={t("Recent Withdraws")}
                        rows={user.recent_withdraws ?? []}
                        columns={[
                            { key: "id", label: "ID" },
                            { key: "amount", label: "Amount" },
                            { key: "status", label: "Status" },
                            { key: "created_at", label: "Date" },
                        ]}
                    />
                </div>
            </Container>
        </AppLayout>
    );
}
