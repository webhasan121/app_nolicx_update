import { usePage, router, Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import UserDash from "../../components/user/dash/UserDash";
import MembershipActivateBox from "../../components/client/MembershipActivateBox";
import NavLink from "../../components/NavLink";
import useTranslation from "../../hooks/useTranslation";
import { formatCurrency } from "../../utils/formatAmount";

export default function Dash() {
    const { props } = usePage();
    const { vendorActive, resellerActive, widgets = [] } = usePage().props;
    const { t } = useTranslation();
    const user = props.auth.user;
    const user_my_ref = props.user_my_ref;
    const hide_claim = props.hide_claim;
    const refClaim = props.ref_claim || { can_apply: !hide_claim, status: "open" };
    const appliedRef = props.applied_ref || "";
    const joined = props.joined;

    const [newRef, setNewRef] = useState(appliedRef);
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        setNewRef(appliedRef);
    }, [appliedRef]);

    const copyRef = async () => {
        const value = user_my_ref || "";

        try {
            if (window.navigator?.clipboard?.writeText) {
                await window.navigator.clipboard.writeText(value);
            } else {
                const input = document.createElement("input");
                input.value = value;
                document.body.appendChild(input);
                input.select();
                document.execCommand("copy");
                document.body.removeChild(input);
            }

            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (error) {
            console.error("Copy failed", error);
        }
    };

    const checkRef = (e) => {
        e.preventDefault();

        router.post(route("user.check.ref"), {
            newRef: newRef,
        });
    };

    return (
        <UserDash>
            <Container>
                <section className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div className="rounded-md bg-white p-4 shadow-md sm:p-6">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                            <div className="min-w-0">
                                <h5 className="text-lg leading-tight">{t("Welcome, back!")}</h5>
                                <h3 className="px-0">
                                    <strong
                                        className="break-words text-green-900"
                                        style={{ fontSize: "22px" }}
                                    >
                                        {user.name.toUpperCase()}
                                    </strong>
                                </h3>
                            </div>
                            <div className="w-full sm:w-auto sm:min-w-[170px]">
                                <p className="mb-2 text-xs text-left sm:text-right">{t("Wallet Balance")}</p>
                                <NavLink
                                    href={route("user.wallet.index")}
                                    className="flex w-full items-center justify-center rounded-lg border px-3 py-2 text-center text-indigo-900 shadow ring-1 sm:w-auto"
                                >
                                    <span className="text-sm text-center break-all">{formatCurrency(user.coin ?? 0)}</span>
                                </NavLink>
                            </div>
                        </div>
                        <p className="mt-3 text-sm leading-6 text-gray-600">
                            {t("We're glad to see you again. Check your dashboard for updates, tasks, and rewards waiting for you today.")}
                        </p>
                    </div>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                        {widgets.map((widget, index) => {
                            const title = index === 0 ? t("Current Level") : t("Upcoming");

                            return (
                                <div
                                    key={`${widget.name}-${index}`}
                                    className="relative rounded-md bg-white p-4 shadow-md sm:p-6"
                                >
                                    <div className="mb-3 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <h6 className="text-sm font-semibold text-gray-600">{title}</h6>
                                        <div className="inline-flex max-w-full items-center justify-center self-start rounded-md bg-blue-600 px-3 py-1 text-xs text-white sm:self-auto sm:text-sm">
                                            <span>{widget.name}</span>
                                        </div>
                                    </div>
                                    {widget.data?.req_users !== undefined && widget.data?.vip_users !== undefined ? (
                                        <>
                                            {index === 0 ? (
                                                <p className="mb-2 mt-2 text-sm font-semibold text-gray-600">
                                                    {t("Achievement")}
                                                </p>
                                            ) : null}
                                            <p className="flex items-center justify-between gap-3 text-xs">
                                                <strong>{t("Normal Users")}</strong>
                                                <span>{widget.data?.req_users}</span>
                                            </p>
                                            <p className="flex items-center justify-between gap-3 text-xs">
                                                <strong>{t("VIP Users")}</strong>
                                                <span>{widget.data?.vip_users}</span>
                                            </p>
                                            {widget.rewards !== null && widget.rewards !== undefined ? (
                                                <p className="mt-3 flex flex-col text-xs leading-5 text-gray-600">
                                                    <strong>{t("Level-Up Rewards")}</strong>
                                                    <span>{widget.rewards}</span>
                                                </p>
                                            ) : null}
                                        </>
                                    ) : null}
                                </div>
                            );
                        })}
                    </div>
                </section>

                {/* Refer & Claim Section */}
                <div className="my-4 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
                    {/* Refer Box */}
                    <SectionSection>
                        <SectionHeader
                            title={t("Refer and Claim")}
                            content={t("Refer your friends and get 5% of every purchase!")}
                        />

                        <SectionInner>
                            <input
                                type="text"
                                readOnly
                                value={user_my_ref || ""}
                                id="refID"
                                disabled
                                className="w-full rounded form-control"
                            />

                            <div className="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <PrimaryButton
                                    onClick={copyRef}
                                    className="my-1 w-full text-right btn btn-success btn-sm PX-3 sm:w-auto"
                                >
                                    <i className="mr-1 fas fa-copy"></i>
                                    {copied ? t("copied") : t("copy")}
                                </PrimaryButton>

                                <NavLink href={route("user.ref.view")} className="text-xs">
                                    {t("View Your Referred User")}
                                </NavLink>
                            </div>
                        </SectionInner>
                    </SectionSection>

                    {/* Claim Box */}
                    {!hide_claim && (
                        <SectionSection>
                            <SectionHeader
                                title={t("Claim Your Reward")}
                                content={t("Your friend may give you a referral code.")}
                            />

                            <SectionInner>
                                {refClaim.can_apply ? (
                                    <form onSubmit={checkRef}>
                                        <input
                                            type="text"
                                            value={newRef}
                                            onChange={(e) =>
                                                setNewRef(e.target.value)
                                            }
                                            placeholder={t("Give Referred Code")}
                                            className="w-full border rounded"
                                        />

                                        <div className="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <PrimaryButton>{t("Apply")}</PrimaryButton>
                                            <div className="text-xs">{joined}</div>
                                        </div>
                                    </form>
                                ) : (
                                    <div>
                                        <input
                                            type="text"
                                            value={refClaim.status === "applied" ? t("Applied") : refClaim.message}
                                            readOnly
                                            disabled
                                            className="w-full border rounded bg-gray-100 text-gray-600"
                                        />

                                        <div className="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <PrimaryButton disabled>
                                                {refClaim.status === "applied" ? t("Applied") : t("Locked")}
                                            </PrimaryButton>
                                            <div className="text-xs">{joined}</div>
                                        </div>
                                    </div>
                                )}
                            </SectionInner>
                        </SectionSection>
                    )}
                </div>

                {/* Upgrade Cards */}
                <SectionSection>
                        {/* membership-activate-box */}
                        <MembershipActivateBox
                            vendorActive={vendorActive}
                            resellerActive={resellerActive}
                        />

                    <style
                        dangerouslySetInnerHTML={{
                            __html: `
      .wrapAdd {
        margin-top: 10px;
        width: 15px;
        height: 15px;
        border-top: 1px solid gray;
        border-right: 1px solid gray;
        transform: rotate(45deg);
        transition: transform 0.3s ease;
      }

      .add:hover > .wrapAdd {
        transform: rotate(45deg) scale(0.7);
      }
    `,
                        }}
                    />
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <Link
                            href={route("upgrade.vendor.index", {
                                upgrade: "vendor",
                            })}
                            className="rounded border p-5 shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                {t("Be a Vendor")}
                            </div>
                            <div className="text-sm">
                                {t("Upgrade your account to")} <strong>{t("VENDOR")}</strong>,
                                {t("sell product and earn commission.")}
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>

                        <Link
                            href={route("upgrade.vendor.index", {
                                upgrade: "reseller",
                            })}
                            className="rounded border p-5 shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                {t("Be Reseller")}
                            </div>
                            <div className="text-sm">
                                {t("Upgrade your account to")}{" "}
                                <strong>{t("Reseller")}</strong> {t("now. Chose product and sel as yours.")}
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>

                        <Link
                            href={route("upgrade.rider.index")}
                            className="rounded border p-5 shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                {t("Be a Rider")}
                            </div>
                            <div className="text-sm">
                                {t("Be a")} <strong>{t("Delevary Man")}</strong>, {t("collect product and shipped to destination.")}
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>
                    </div>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
