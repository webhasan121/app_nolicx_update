import { usePage, router, Link } from "@inertiajs/react";
import { useState } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import UserDash from "../../components/user/dash/UserDash";
import NavLink from "../../components/NavLink";

export default function Dash() {
    const { props } = usePage();
    const { vendorActive, resellerActive } = usePage().props;
    const user = props.auth.user;
    const user_my_ref = props.user_my_ref;
    const hide_claim = props.hide_claim;
    const joined = props.joined;

    const [newRef, setNewRef] = useState("");
    const [copied, setCopied] = useState(false);

    const copyRef = () => {
        navigator.clipboard.writeText(user_my_ref || "");
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
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
                {/* Welcome Section */}
                <SectionSection>
                    <div className="my-2 rounded">
                        <div className="flex justify-between">
                            <div>Welcome, back!</div>
                        </div>

                        <div className="flex items-center justify-between">
                            <b className="text-xl text-green-900">
                                {user.name.toUpperCase()}
                            </b>

                            <NavLink
                                href={route("user.wallet.index")}
                                className="px-3 text-indigo-900 border rounded-lg shadow ring-1"
                            >
                                <div>Wallet</div>
                                <div className="py-1 pl-3">
                                    {user.coin ?? 0} TK
                                </div>
                            </NavLink>
                        </div>
                    </div>
                </SectionSection>

                {/* Refer & Claim Section */}
                <div className="justify-between gap-4 my-2 lg:flex">
                    {/* Refer Box */}
                    <SectionSection>
                        <SectionHeader
                            title="Refer and Claim"
                            content="Refer your friends and get 5% of every purchase!"
                        />

                        <SectionInner>
                            <input
                                type="text"
                                readOnly
                                value={user_my_ref || ""}
                                className="w-full border rounded"
                            />

                            <div className="flex items-center mt-2">
                                <PrimaryButton onClick={copyRef}>
                                    {copied ? "Copied!" : "Copy"}
                                </PrimaryButton>

                                <NavLink href={route("user.ref.view")}>
                                    View Your Referred User
                                </NavLink>
                            </div>
                        </SectionInner>
                    </SectionSection>

                    {/* Claim Box */}
                    {!hide_claim && (
                        <SectionSection>
                            <SectionHeader
                                title="Claim Your Reward"
                                content="Your friend may give you a referral code."
                            />

                            <SectionInner>
                                <form onSubmit={checkRef}>
                                    <input
                                        type="text"
                                        value={newRef}
                                        onChange={(e) =>
                                            setNewRef(e.target.value)
                                        }
                                        disabled={user.reference_accepted_at}
                                        placeholder="Give Referred Code"
                                        className="w-full border rounded"
                                    />

                                    <div className="flex items-center justify-between mt-2">
                                        <PrimaryButton>Apply</PrimaryButton>

                                        <div className="text-xs">{joined}</div>
                                    </div>
                                </form>
                            </SectionInner>
                        </SectionSection>
                    )}
                </div>

                {/* Upgrade Cards */}
                <SectionSection>
                    <div>
                        {/* membership-activate-box */}
                        {vendorActive && (
                            <div className="px-4 py-3 mb-3 text-green-700 bg-green-100 border border-green-400 rounded">
                                <h6 className="mb-2 font-semibold">Hello,</h6>

                                <p>
                                    Your request for vendor, name of
                                    <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                                        {vendorActive.shop_name_bn ?? "N/A"} /
                                        {vendorActive.shop_name_en ?? "N/A"}
                                    </strong>
                                    with
                                    <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                                        {vendorActive.system_get_comission ??
                                            "0"}
                                        %
                                    </strong>
                                    commission share, is active now.
                                </p>

                                <NavLink
                                    href={route("dashboard")}
                                    className="inline-block mt-2"
                                >
                                    Go To Dashboard
                                </NavLink>
                            </div>
                        )}

                        {/* Reseller Active */}
                        {resellerActive && (
                            <div className="px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded">
                                <h6 className="mb-2 font-semibold">Hello,</h6>

                                <p>
                                    Your request for reseller, name of
                                    <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                                        {resellerActive.shop_name_bn ?? "N/A"} /
                                        {resellerActive.shop_name_en ?? "N/A"}
                                    </strong>
                                    with
                                    <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                                        {resellerActive.system_get_comission ??
                                            "0"}
                                        %
                                    </strong>
                                    commission share, is active now.
                                </p>
                            </div>
                        )}
                    </div>

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
                    <div
                        className="grid gap-3"
                        style={{
                            gridTemplateColumns:
                                "repeat(auto-fill, minmax(200px, 1fr))",
                        }}
                    >
                        <Link
                            href={route("upgrade.vendor.index", {
                                upgrade: "vendor",
                            })}
                            className="p-3 border rounded shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                Be a Vendor
                            </div>
                            <div className="text-sm">
                                Upgrade your account to <strong>VENDOR</strong>,
                                sell product and earn commission.
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>

                        <Link
                            href={route("upgrade.vendor.index", {
                                upgrade: "reseller",
                            })}
                            className="p-3 border rounded shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                Be Reseller
                            </div>
                            <div className="text-sm">
                                Upgrade your account to{" "}
                                <strong>Reseller</strong> now. Chose product and
                                sel as yours.
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>

                        <Link
                            href={route("upgrade.rider.index")}
                            className="p-3 border rounded shadow"
                            style={{
                                background:
                                    "linear-gradient(135deg, #ebebeb, lightgreen, #ebebeb)",
                            }}
                        >
                            <div className="text-lg font-semibold text-green-600">
                                Be a Rider
                            </div>
                            <div className="text-sm">
                                Be a <strong>Delevary Man</strong>, collect
                                product and shipped to destination.
                            </div>
                            <div className="wrapAdd"></div>
                        </Link>
                    </div>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
