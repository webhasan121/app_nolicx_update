import AppLayout from "../../Layouts/App";
import Container from "../../components/dashboard/Container";
import useTranslation from "../../hooks/useTranslation";

function DetailRow({ label, value, children }) {
    return (
        <div className="grid gap-1 border-b border-gray-100 px-4 py-3 last:border-b-0 md:grid-cols-[180px_1fr] md:items-start">
            <div className="text-sm font-medium text-gray-500">{label}</div>
            <div className="min-w-0 text-sm font-semibold text-gray-900 md:text-right">
                {value}
                {children}
            </div>
        </div>
    );
}

function NidImage({ title, src, alt }) {
    if (!src) {
        return null;
    }

    return (
        <div className="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
            <div className="border-b border-gray-100 px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                {title}
            </div>
            <div className="bg-gray-50 p-3">
                <img
                    src={src}
                    className="h-40 w-full rounded border border-gray-100 bg-white object-contain"
                    alt={alt}
                />
            </div>
        </div>
    );
}

export default function RiderInfoPage({ rider = {} }) {
    const { t } = useTranslation();

    return (
        <AppLayout title={t("My Rider")}>
            <Container>
                <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div className="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 bg-white px-5 py-4">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">{rider.name}</h2>
                            <div className="mt-2">
                                {rider.is_reject ? (
                                    <div>
                                        <span className="inline-flex rounded bg-red-700 px-3 py-1 text-xs font-semibold text-white shadow">
                                            {t("Rejected")}
                                        </span>
                                        <div className="mt-1 text-xs text-red-600">{rider.reject_fo}</div>
                                    </div>
                                ) : (
                                    <span className="inline-flex rounded bg-gray-900 px-3 py-1 text-xs font-semibold text-white shadow">
                                        {t(rider.status)}
                                    </span>
                                )}
                            </div>
                        </div>
                        <div className="text-sm text-gray-500">{t("from")} {rider.joined ?? t("N/A")}</div>
                    </div>

                    <div className="bg-green-50 px-5 py-4">
                        <div className="flex flex-wrap items-center justify-between gap-2">
                            <div className="text-sm font-medium text-green-800">{t("Target Area")}</div>
                            <div className="text-sm font-bold text-green-950">{rider.targeted_area ?? t("N/A")}</div>
                        </div>
                    </div>

                    <div className="divide-y divide-gray-100">
                        <DetailRow label={t("Name")} value={rider.name} />
                        <DetailRow label={t("Email")} value={rider.email} />
                        <DetailRow label={t("Phone")} value={rider.phone} />
                        <DetailRow label={t("Permanent Address")} value={rider.fixed_address} />
                        <DetailRow label={t("Current Address")} value={rider.current_address} />
                        <DetailRow label={t("NID")} value={rider.nid}>
                            <div className="mt-3 grid w-full gap-4 text-left sm:grid-cols-2 lg:ml-auto lg:w-[760px]">
                                <NidImage title={t("Front")} src={rider.nid_photo_front_url} alt={t("NID front")} />
                                <NidImage title={t("Back")} src={rider.nid_photo_back_url} alt={t("NID back")} />
                            </div>
                        </DetailRow>
                    </div>
                </div>
            </Container>
        </AppLayout>
    );
}
