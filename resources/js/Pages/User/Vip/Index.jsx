import { usePage } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import PrimaryButton from "../../../components/PrimaryButton";
import Dashboard from "../../Dashboard";
import UserDash from "../../../components/user/dash/UserDash";
import SectionInner from "../../../components/dashboard/section/Inner";
import PackageRequest from "../../../components/PackageRequest";
import Modal from "../../../components/Modal";
import VipCart from "../../../components/VipCart";
import useTranslation from "../../../hooks/useTranslation";

export default function VipIndex() {
    const { t } = useTranslation();
    const { vip, packages } = usePage().props;
    const [show, setShow] = useState(false);

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <div className="flex items-center justify-between">
                        <SectionHeader
                            title={t("VIP Package")}
                            content={t("your vip package, visit packages and purchase one.")}
                        />
                        <PrimaryButton onClick={() => setShow(true)}>{t("Purchase")}</PrimaryButton>
                    </div>
                </SectionSection>

                <div>
                    <SectionSection>
                        <SectionHeader
                            title={t("Your Subscription")}
                            content={t("You has subscribe our bellow package. To veiw details click on 'VIEW DETAILS' button, on package cart.")}
                        />
                        <SectionInner>
                            <PackageRequest isRequestedAccepted={vip} />
                        </SectionInner>
                    </SectionSection>
                    {!vip || vip.length === 0 ? (
                        <div className="py-10 text-center">
                            <div className="text-lg font-bold">{t("No Active Package Found")}</div>
                        </div>
                    ) : null}
                </div>
                <Modal show={show} onClose={() => setShow(false)}>
                    <div
                        style={{
                            display: "grid",
                            gridTemplateColumns:
                                "repeat(auto-fit, minmax(250px, 1fr))",
                            gap: "20px",
                            padding: "30px 50px",
                        }}
                    >
                        {packages.map((item) => (
                            <VipCart
                                key={item.id}
                                item={item}
                                type="owner"
                                active=""
                            />
                        ))}
                    </div>
                </Modal>
            </Container>
        </UserDash>
    );
}
