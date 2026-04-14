import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import InputLabel from "../../../../components/InputLabel";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function View({ withdraw }) {
    const confirmForm = useForm({
        paid_from: "",
        trx: "",
    });

    const rejectForm = useForm({
        rMessage: "",
    });

    const confirmPayment = (e) => {
        e.preventDefault();
        confirmForm.post(route("system.withdraw.confirm", { id: withdraw.id }));
    };

    const rejectPayment = (e) => {
        e.preventDefault();
        rejectForm.post(route("system.withdraw.reject", { id: withdraw.id }));
    };

    return (
        <AppLayout
            title="Withdraws"
            header={
                <PageHeader>
                    Withdraws
                    <br />
                    <div className="text-2xl">
                        {withdraw?.user?.currency_sing}{withdraw?.amount}
                        <div className="text-sm text-gray-400">
                            {!withdraw?.is_rejected ? (
                                withdraw?.status ? "Accept" : "Pending"
                            ) : (
                                <div className="p-1">Reject</div>
                            )}
                        </div>
                    </div>
                </PageHeader>
            }
        >
            <Head title="Withdraw Details" />

            <Container>
                <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, 170px)", gridGap: "10px", justifyContent: "start", alignItems: "start" }}>
                    <div className="p-3 w-full bg-white rounded-lg shadow-sm">
                        Withdrawal
                        <Hr />
                        <div className="text-5xl font-bold my-3">
                            {withdraw?.amount} <span className="text-sm"> {withdraw?.user?.currency_sing} </span>
                        </div>
                        User Balance
                        <Hr />
                        <div className=" text-xl font-bold">
                            {withdraw?.user?.abail_coin}
                        </div>
                    </div>

                    <div className="p-3 w-full bg-white rounded-lg shadow-sm">
                        Payable
                        <Hr />
                        <div className="text-5xl font-bold my-3">
                            {withdraw?.payable_amount ?? 0} <span className="text-sm"> {withdraw?.user?.currency_sing}
                            </span>
                        </div>
                        Range
                        <Hr />
                        <div className=" text-xl font-bold">
                            {withdraw?.fee_range ?? "0"} %
                        </div>
                    </div>

                    <div className="p-3 w-full bg-white rounded-lg shadow-sm">
                        VAT
                        <Hr />
                        <div className="text-5xl font-bold my-3">
                            {withdraw?.total_fee ?? 0} <span className="text-sm"> {withdraw?.user?.currency_sing} </span>
                        </div>
                        deduction
                        <Hr />
                        <div className=" text-xl font-bold">
                            {withdraw?.server_fee ?? 0} + {withdraw?.maintenance_fee ?? 0}
                        </div>
                    </div>
                </div>

                <div className={`p-3 bg-red-600 text-white rounded-lg ${!withdraw?.is_rejected ? "hidden" : ""}`}>
                    <SectionHeader
                        title={<span className="text-white">Request Rejected !</span>}
                        content={<span className="text-white">{withdraw?.reject_for ?? " Unknown reason"}</span>}
                    />
                </div>

                <Section className={`${!withdraw?.status ? "hidden" : ""} bg-indigo-900 text-white`}>
                    <SectionHeader
                        title={<div className="text-white">Request Confirmed !</div>}
                        content={`Confirm by ${withdraw?.confirm_by ?? "N/A"} at ${withdraw?.updated_at_formatted ?? ""}`}
                    />
                </Section>

                {!withdraw?.status && !withdraw?.is_rejected ? (
                    <Section className="mt-2">
                        <SectionHeader
                            title="Payment Confirmation"
                            content="Confirm payment and make a track to your payment history."
                        />
                        <Hr />

                        <SectionInner>
                            <div className="">
                                <div className="inline-block mt-2">
                                    <div className="border bg-indigo-900 text-white overflow-hidden rounded-lg w-full">
                                        <div className="p-3">
                                            User Request
                                        </div>
                                        <div className=" p-3">
                                            <strong>
                                                {withdraw?.pay_by}
                                            </strong>
                                            <br />
                                            {withdraw?.pay_to}
                                            <Hr />
                                        </div>
                                        <div className=" p-3">
                                            <strong>
                                                {withdraw?.bank_account}
                                            </strong>
                                            {withdraw?.account_holder_name}
                                            {withdraw?.account_humber}
                                        </div>
                                    </div>
                                </div>

                                <div className="p-2 rounded-lg mt-2 border">
                                    <form onSubmit={confirmPayment}>
                                        <div className="md:flex justify-between items-center py-1 my-1">
                                            <div>
                                                Payment From
                                            </div>
                                            <TextInput value={confirmForm.data.paid_from} onChange={(e) => confirmForm.setData("paid_from", e.target.value)} className="" placeholder="From where the payment has been done" />
                                        </div>
                                        <div className="text-xs">
                                            Bank Account or Mobile Banking Number that user receive the payment.
                                        </div>
                                        {confirmForm.errors.paid_from ? (
                                            <div className="text-red-900">
                                                {confirmForm.errors.paid_from}
                                            </div>
                                        ) : null}
                                        <Hr />
                                        <div className="md:flex justify-between items-center py-1 my-1">
                                            <div>
                                                TRX ID
                                            </div>
                                            <TextInput value={confirmForm.data.trx} onChange={(e) => confirmForm.setData("trx", e.target.value)} className="" placeholder="TRX ID" />
                                        </div>
                                        {confirmForm.errors.trx ? (
                                            <div className="text-red-900">
                                                {confirmForm.errors.trx}
                                            </div>
                                        ) : null}
                                        <Hr />

                                        <PrimaryButton>Confirm Payment</PrimaryButton>
                                    </form>
                                </div>
                            </div>
                        </SectionInner>
                    </Section>
                ) : null}

                <Section className={`${withdraw?.status || withdraw?.is_rejected ? "hidden" : ""}`}>
                    <SectionHeader
                        title="Payment Reejction"
                        content="Have an unprocessable payment? Wish to reject the payment requeest. Payment rejection process done by following procedures."
                    />
                    <Hr />
                    <form onSubmit={rejectPayment}>
                        <InputLabel htmlFor="withdraw-reject-message">Rejection Message</InputLabel>
                        <textarea
                            id="withdraw-reject-message"
                            value={rejectForm.data.rMessage}
                            onChange={(e) => rejectForm.setData("rMessage", e.target.value)}
                            className="w-full rounded-lg"
                            placeholder="Write Your Rejection Message .......... "
                            rows="4"
                        ></textarea>
                        {rejectForm.errors.rMessage ? (
                            <div className="text-red-900">
                                {rejectForm.errors.rMessage}
                            </div>
                        ) : null}
                        <DangerButton type="submit">Confirm Reject</DangerButton>
                    </form>
                </Section>
            </Container>
        </AppLayout>
    );
}
