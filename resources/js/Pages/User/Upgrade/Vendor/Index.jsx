import { usePage } from "@inertiajs/react";
import { useState } from "react";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import Hr from "../../../../components/Hr";
import UserDash from "../../../../components/user/dash/UserDash";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import MembershipActivateBox from "../../../../components/client/MembershipActivateBox";

export default function UpgradeVendorIndex() {
    const {
        upgrade = "vendor",
        vendor_requests = [],
        reseller_requests = [],
        vendor_active,
        reseller_active,
    } = usePage().props;

    const [showCreateModal, setShowCreateModal] = useState(false);
    const rows = upgrade === "reseller" ? reseller_requests : vendor_requests;

    return (
        <UserDash>
            <SectionSection>
                <SectionHeader
                    title={<h5>Profile Upgrade ({upgrade})</h5>}
                    content={
                        <>
                            <div className="flex justify-between">
                                <div>
                                    Upgrade your account to revenew money. To make a new request , click the
                                </div>
                            </div>
                            <PrimaryButton type="button" onClick={() => setShowCreateModal(true)}>
                                NEW REQUEST
                            </PrimaryButton>
                            <Hr />
                            <MembershipActivateBox
                                vendorActive={vendor_active}
                                resellerActive={reseller_active}
                            />
                        </>
                    }
                />

                <Hr />
                <div>
                    <NavLink
                        active={upgrade === "vendor"}
                        href={route("upgrade.vendor.index", { upgrade: "vendor" })}
                    >
                        Vendor
                    </NavLink>
                    <NavLink
                        active={upgrade === "reseller"}
                        href={route("upgrade.vendor.index", { upgrade: "reseller" })}
                    >
                        Reseller
                    </NavLink>
                    <NavLink
                        active={upgrade === "rider"}
                        href={route("upgrade.rider.index")}
                    >
                        Rider
                    </NavLink>
                </div>

                <SectionInner>
                    {rows.length > 0 ? (
                        <SectionInner>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((vr, idx) => (
                                        <tr key={vr.id}>
                                            <td>{idx + 1}</td>
                                            <td>
                                                <NavLink
                                                    href={route("upgrade.vendor.edit", {
                                                        id: vr.id,
                                                        upgrade,
                                                    })}
                                                >
                                                    {vr.shop_name_en}
                                                </NavLink>
                                            </td>
                                            <td>{vr.created_at}</td>
                                            <td>{vr.status}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </SectionInner>
                    ) : (
                        <div className="alert alert-info">
                            No Previous request found! Make a new request, instead.
                        </div>
                    )}
                </SectionInner>
            </SectionSection>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} maxWidth="sm">
                <SectionSection>
                    <SectionHeader
                        title="Make sure your request"
                        content="please choose your expected link to reqeust."
                    />
                    <SectionInner>
                        <NavLink href={route("upgrade.vendor.create", { upgrade: "vendor" })}>
                            <PrimaryButton>Request for Vendor</PrimaryButton>
                        </NavLink>
                        <br />
                        <NavLink href={route("upgrade.vendor.create", { upgrade: "reseller" })}>
                            <PrimaryButton>Request for Reseller</PrimaryButton>
                        </NavLink>
                        <br />
                        <NavLink href={route("upgrade.rider.create")}>
                            <PrimaryButton>Request for Rider (Delevary Man)</PrimaryButton>
                        </NavLink>
                    </SectionInner>
                </SectionSection>
            </Modal>
        </UserDash>
    );
}
