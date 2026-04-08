import AppLayout from "../../Layouts/App";
import Container from "../../components/dashboard/Container";
import Section from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import Hr from "../../components/Hr";

function DetailRow({ label, value, children }) {
    return (
        <div className="items-center justify-between mb-1 bg-gray-100 border-b md:flex">
            <div className="p-2">{label}</div>
            <div className="p-2">
                {value}
                {children}
            </div>
        </div>
    );
}

export default function RiderInfoPage({ rider = {} }) {
    return (
        <AppLayout title="My Rider">
            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>{rider.name}</div>
                                <div className="text-sm">from {rider.joined ?? "N/A"}</div>
                            </div>
                        }
                        content={
                            rider.is_reject ? (
                                <div>
                                    <div className="inline-flex px-3 py-1 text-xs text-white bg-red-700 rounded shadow">
                                        Rejected
                                    </div>
                                    <div className="text-xs">{rider.reject_fo}</div>
                                </div>
                            ) : (
                                <div className="inline-block px-3 py-1 text-xs text-white bg-gray-800 rounded shadow">
                                    {rider.status}
                                </div>
                            )
                        }
                    />

                    <SectionInner>
                        <div className="items-center justify-between mb-1 bg-green-100 border-b md:flex">
                            <div className="p-2">Target Area</div>
                            <div className="p-2 text-bold">{rider.targeted_area ?? "N/A"}</div>
                        </div>

                        <Hr />

                        <DetailRow label="Name" value={rider.name} />
                        <DetailRow label="Email" value={rider.email} />
                        <DetailRow label="Phone" value={rider.phone} />
                        <DetailRow label="Permanent Address" value={rider.fixed_address} />
                        <DetailRow label="Current Address" value={rider.current_address} />
                        <DetailRow label="NID" value={rider.nid}>
                            <hr />
                            {rider.nid_photo_front_url ? (
                                <img
                                    src={rider.nid_photo_front_url}
                                    className="w-12 h-12 rounded shadow"
                                    alt=""
                                />
                            ) : null}
                            {rider.nid_photo_back_url ? (
                                <img
                                    src={rider.nid_photo_back_url}
                                    className="w-12 h-12 rounded shadow"
                                    alt=""
                                />
                            ) : null}
                        </DetailRow>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
