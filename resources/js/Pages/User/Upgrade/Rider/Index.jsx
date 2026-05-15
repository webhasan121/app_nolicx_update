import { usePage } from "@inertiajs/react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import UserDash from "../../../../components/user/dash/UserDash";
import useTranslation from "../../../../hooks/useTranslation";

export default function UpgradeRiderIndex() {
    const { t } = useTranslation();
    const { rider = [] } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <h5>Rider</h5>
                                <NavLinkBtn href={route("upgrade.rider.create")}>
                                    <i className="fas fa-plus pr-2"></i> New
                                </NavLinkBtn>
                            </div>
                        }
                        content={
                            <div className="flex justify-between">
                                <div></div>
                            </div>
                        }
                    />

                    <SectionInner>
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns: "repeat(auto-fit, 160px)",
                                gridGap: "10px",
                            }}
                        >
                            {rider.map((item) => (
                                <div
                                    key={item.id}
                                    className="rounded relative bg-gray-100 shadow"
                                >
                                    <div className="cart-header flex items-center justify-center py-3">
                                        <i
                                            className="fas fa-truck-fast"
                                            style={{ fontSize: "60px" }}
                                        ></i>
                                    </div>
                                    <div>
                                        <div className="p-2">
                                            <div className="text-xs">{t("Phone :")}</div>
                                            <div className="text-sm text-md font-bold">
                                                {item.phone}
                                            </div>
                                        </div>
                                        <hr />
                                        <div className="p-2">
                                            <div className="text-xs">
                                                {" "}{t("Tergeted Area :")}{" "}
                                            </div>
                                            <div className="text-sm text-md font-bold">
                                                {item.targeted_area}
                                            </div>
                                        </div>
                                        <hr />
                                        <div className="p-2">
                                            <div className="text-xs">
                                                {" "}{t("Create Date :")}{" "}
                                            </div>
                                            <div className="text-sm text-md font-bold">
                                                {item.created_at}
                                            </div>
                                        </div>

                                        <Hr />
                                        <div className="p-2">
                                            {item.status === "Pending" && (
                                                <div
                                                    className="text-xs w-full p-1 px-2"
                                                    style={{
                                                        backgroundColor:
                                                            "#fefcbf",
                                                        color: "#b45309",
                                                    }}
                                                >
                                                    <strong>{t("Pending")}</strong>
                                                </div>
                                            )}
                                            {item.status === "Active" && (
                                                <div
                                                    className="text-xs w-full p-1 px-2"
                                                    style={{
                                                        backgroundColor:
                                                            "#bbf7d0",
                                                        color: "#166534",
                                                    }}
                                                >
                                                    <strong>{t("Active")}</strong>
                                                </div>
                                            )}
                                            {item.is_rejected && (
                                                <div
                                                    className="text-xs w-full p-1 px-2"
                                                    style={{
                                                        backgroundColor:
                                                            "#fee2e2",
                                                        color: "#b91c1c",
                                                    }}
                                                >
                                                    <strong>{t("Rejected")}</strong>
                                                </div>
                                            )}
                                        </div>
                                        {item.status === "Pending" && (
                                            <>
                                                <hr />
                                                <div className="p-2">
                                                    <NavLink
                                                        href={route(
                                                            "upgrade.rider.edit",
                                                            { id: item.id },
                                                        )}
                                                    >
                                                        <i className="fas fa-edit pr-2"></i>{" "}{t("Edit")}</NavLink>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
