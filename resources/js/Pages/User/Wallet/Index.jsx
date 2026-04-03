import { Link, usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import UserDash from "../../../components/user/dash/UserDash";



function EarningCard({ title, amount, href }) {
    return (
        <div className="w-48 space-y-3">
            <div className="p-3 rounded-lg shadow-md">
                <div>
                    <div className="">{title}</div>
                </div>
                <div className="pt-2 text-lg font-bold text-indigo-900">
                    {amount ?? 0} TK
                </div>
                <div className="text-xs">
                    <Link href={href} className="text-gray-600">
                        View All
                    </Link>
                </div>
            </div>
        </div>
    );
}

export default function WalletIndex() {
    const {
        available_balance,
        task,
        comission,
        cut,
        reffer,
        withdraw,
    } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <div className="items-center justify-between md:flex">
                        <SectionHeader
                            title="Your Wallet"
                            content={
                                <div className="text-2xl font-bold text-indigo-900">
                                    {" "}
                                    Available Balance {available_balance} TK{" "}
                                </div>
                            }
                        />
                        <Link
                            href={route("user.wallet.withdraw")}
                            className="px-2 py-2 text-sm font-bold uppercase border-0 rounded-lg ring-1"
                        >
                            Withdraw
                        </Link>
                    </div>
                </SectionSection>

                <SectionSection>
                    <SectionHeader title="Todays Earning" />
                    <SectionInner>
                        <div className="flex flex-wrap items-start justify-start space-x-3 spacy-y-3">
                            <EarningCard
                                title="Task"
                                amount={task?.coin ?? 0}
                                href={route("user.wallet.tasks")}
                            />
                            <EarningCard
                                title="Earn Comission"
                                amount={comission}
                                href={route("user.wallet.earn-comissions")}
                            />
                            <EarningCard
                                title="Cut Comission"
                                amount={cut}
                                href={route("user.wallet.earn-comissions", {
                                    nav: "system",
                                })}
                            />
                            <EarningCard
                                title="VIP Reffer"
                                amount={reffer}
                                href={route("user.wallet.reffer")}
                            />
                        </div>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader title="Withdraws Requests" />
                    <SectionInner>
                        {withdraw?.length ? (
                            <div>
                                {withdraw.map((item) => (
                                    <div
                                        key={item.id}
                                        className="flex items-center justify-between p-2 border rounded"
                                    >
                                        <div>#{item.id}</div>
                                        <div>{item.amount} TK</div>
                                        <div>{item.status}</div>
                                        <div className="text-xs text-gray-500">
                                            {item.created_at} - {item.created_at_human}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div>No Withdraw Info Found !</div>
                        )}
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
