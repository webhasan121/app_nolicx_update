import { router, usePage } from "@inertiajs/react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import UserDash from "../../../../components/user/dash/UserDash";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import useTranslation from "../../../../hooks/useTranslation";
import { formatAmount } from "../../../../utils/formatAmount";

export default function WithdrawIndex() {
    const { t } = useTranslation();
    const { wallet_balance, available_balance, withdraw } = usePage().props;
    const cancelWithdraw = (wid) => {
        router.post(route("user.withdraw.destroy"), { wid });
    };

    return (
        <UserDash>
            <div className="p-2">
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex items-start justify-between">
                                    <div>{t("Your Wallet")}</div>
                                </div>
                            }
                            content={
                                <div>
                                    <div className="text-2xl font-bold text-indigo-900">
                                        {" "}{t("Wallet Balance")}-{formatAmount(wallet_balance)}{t("TK")}
                                    </div>
                                    <div className="text-sm text-gray-600">
                                        {t("Withdrawable Balance")}: {formatAmount(available_balance)}{t("TK")}
                                    </div>
                                </div>
                            }
                        />

                        <SectionInner>
                            <ul>
                                <li>
                                    {t("To make a withdrawal, your balance must be at least 500 TK. If you're a new user, you'll need to reach a minimum balance of 500 TK before you can withdraw.")}
                                </li>
                                <li>
                                    {t("To make a withdrawal, VIP and VIP Package users must first complete a product purchase.")}
                                </li>
                            </ul>

                            <Hr />
                            <div className="mt-2 space-x-2 text-end">
                                <NavLinkBtn href={route("user.wallet.withdraw.create")}>
                                    {t("Request A Payment")}
                                </NavLinkBtn>
                            </div>
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <div className="flex items-center justify-between">
                            <div>{t("Last Activity")}</div>
                            <NavLink href="">{t("History")}</NavLink>
                        </div>

                        <div className="mt-2">
                            <div className="m-0 row">
                                {withdraw?.map((wtd) =>
                                    wtd.status === 0 && wtd.is_rejected == null ? (
                                        <div key={wtd.id} className="w-48 py-3">
                                            <div className="text-left border rounded">
                                                <div className="px-3 py-2 border-bottom">
                                                    <h6>{t("Status")}</h6>
                                                    <p className="font-bold text-red-900">{t("Pending")}</p>
                                                </div>
                                                <div className="px-3 py-2 border-b">
                                                    <h6>{t("Amount")}</h6>
                                                    <p className="font-bold">
                                                        {formatAmount(wtd.amount)}{t("TK")}</p>
                                                </div>
                                                <div className="px-3 py-2 border-b">
                                                    <p>{wtd.pay_by}</p>
                                                    <p className="font-bold">{t("A/C:")}{wtd.pay_to}
                                                    </p>
                                                </div>
                                                <div className="p-3">
                                                    <h6>{t("Date")}</h6>
                                                    <p>
                                                        {wtd.created_at}
                                                        <br />- {wtd.created_at_human}
                                                    </p>
                                                    <Hr />
                                                    <form
                                                        className={`${wtd.is_rejected ? "d-none" : "d-block"}`}
                                                        onSubmit={(e) => {
                                                            confirm(t("Are you sure to cancel this withdraw request?")) &&
                                                            e.preventDefault();
                                                            cancelWithdraw(wtd.id);
                                                        }}
                                                    >
                                                        <input type="hidden" name="wid" value={wtd.id} />
                                                        <button
                                                            type="submit"
                                                            className="px-2 bg-red-900 border rounded"
                                                        >{t("Cancel")}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    ) : null,
                                )}
                            </div>
                        </div>
                    </SectionSection>
                </Container>
            </div>
        </UserDash>
    );
}
