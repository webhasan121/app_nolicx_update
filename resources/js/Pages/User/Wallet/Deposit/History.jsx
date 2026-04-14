import { useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import UserDash from "../../../../components/user/dash/UserDash";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import InputLabel from "../../../../components/InputLabel";
import TextInput from "../../../../components/TextInput";
import Table from "../../../../components/dashboard/table/Table";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";

export default function DepositHistory() {
    const { coin = 0, history = [], payNumbers = {} } = usePage().props;
    const [showModal, setShowModal] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        amount: "",
        paymentMethod: "",
        receiverAccountNumber: "",
        senderName: "",
        senderAccountNumber: "",
        transactionId: "",
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("user.wallet.diposit.store"), {
            onSuccess: () => {
                reset();
                setShowModal(false);
            },
        });
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Deposit To Wallet"
                        content={
                            <div className="items-center justify-between md:flex">
                                <div className="text-2xl font-bold text-indigo-900">
                                    {coin} TK
                                </div>
                                <div className="flex">
                                    <NavLinkBtn
                                        className="px-2 border-0 rounded-lg ring-1 uppercase font-bold"
                                        href={route("user.wallet.withdraw")}
                                    >
                                        Withdraw
                                    </NavLinkBtn>
                                </div>
                            </div>
                        }
                    />
                </SectionSection>

                <SectionSection>
                    <p className="text-sm">
                        Deposit amount to your wallet. To make confirm your
                        deposit, you are requested to send your expected amout
                        to our Mobile Bank Account (Bkash, Nogod, Roket).
                        {Object.entries(payNumbers).map(([type, pay]) => (
                            <div
                                key={type}
                                className="inline-flex p-2 mb-1 border rounded"
                            >
                                <span className="pr-2 font-bold">{type}:</span>{" "}
                                {pay}
                            </div>
                        ))}
                    </p>
                    <Hr />
                    <PrimaryButton onClick={() => setShowModal(true)}>
                        <i className="px-2 fas fa-plus"></i> Deposit
                    </PrimaryButton>
                </SectionSection>

                <SectionSection>
                    <div>History</div>

                    <SectionInner>
                        <Table data={history}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Trx ID</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {history.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.amount}</td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.senderAccountNumber}{" "}
                                                <i className="px-2 fas fa-caret-right"></i>
                                                {item.paymentMethod}{" "}
                                                <i className="px-2 fas fa-caret-right"></i>
                                                {item.receiverAccountNumber}
                                            </div>
                                        </td>
                                        <td>{item.transactionId}</td>
                                        <td>
                                            {item.confirmed
                                                ? "Confirmed"
                                                : "Pending"}
                                        </td>
                                        <td>{item.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>

                <Modal show={showModal} onClose={() => setShowModal(false)} maxWidth="md">
                    <div className="p-4">
                        <div className="text-lg">Deposit</div>

                        <Hr />

                        <form onSubmit={submit}>
                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-amount">Amount</InputLabel>
                                <TextInput
                                    id="deposit-amount"
                                    type="number"
                                    value={data.amount}
                                    onChange={(e) =>
                                        setData("amount", e.target.value)
                                    }
                                    className="w-full"
                                    placeholder="Enter amount"
                                />
                                {errors.amount && (
                                    <div className="text-red-500">
                                        {errors.amount}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-paymentMethod">Payment Method</InputLabel>
                                <select
                                    id="deposit-paymentMethod"
                                    className="w-full rounded"
                                    value={data.paymentMethod}
                                    onChange={(e) =>
                                        setData(
                                            "paymentMethod",
                                            e.target.value,
                                        )
                                    }
                                >
                                    <option value="">
                                        Select Payment Method
                                    </option>
                                    <option value="bkash">Bkash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="rocket">Rocket</option>
                                    <option value="Bank">Bank</option>
                                </select>
                                {errors.paymentMethod && (
                                    <div className="text-red-500">
                                        {errors.paymentMethod}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-receiverAccountNumber">Receiver Account Number</InputLabel>
                                <TextInput
                                    id="deposit-receiverAccountNumber"
                                    type="text"
                                    value={data.receiverAccountNumber}
                                    onChange={(e) =>
                                        setData(
                                            "receiverAccountNumber",
                                            e.target.value,
                                        )
                                    }
                                    className="w-full"
                                    placeholder="Enter account number"
                                />
                                {errors.receiverAccountNumber && (
                                    <div className="text-red-500">
                                        {errors.receiverAccountNumber}
                                    </div>
                                )}
                                <div className="text-xs">
                                    If you send throught the bank, your are
                                    requested to write Bank Name first. Then
                                    Back Account Number.
                                </div>
                            </div>

                            <Hr />
                            <div className="text-xs">Sender Info</div>
                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-senderAccountNumber">Sender Account Number</InputLabel>
                                <TextInput
                                    id="deposit-senderAccountNumber"
                                    type="text"
                                    value={data.senderAccountNumber}
                                    onChange={(e) =>
                                        setData(
                                            "senderAccountNumber",
                                            e.target.value,
                                        )
                                    }
                                    className="w-full"
                                    placeholder="Enter account number"
                                />
                                {errors.senderAccountNumber && (
                                    <div className="text-red-500">
                                        {errors.senderAccountNumber}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-senderName">Sender Name</InputLabel>
                                <TextInput
                                    id="deposit-senderName"
                                    type="text"
                                    value={data.senderName}
                                    onChange={(e) =>
                                        setData("senderName", e.target.value)
                                    }
                                    className="w-full"
                                    placeholder="Enter sender name"
                                />
                                {errors.senderName && (
                                    <div className="text-red-500">
                                        {errors.senderName}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-transactionId">Transaction ID</InputLabel>
                                <TextInput
                                    id="deposit-transactionId"
                                    type="text"
                                    value={data.transactionId}
                                    onChange={(e) =>
                                        setData(
                                            "transactionId",
                                            e.target.value,
                                        )
                                    }
                                    className="w-full"
                                    placeholder="Enter transaction ID"
                                />
                                {errors.transactionId && (
                                    <div className="text-red-500">
                                        {errors.transactionId}
                                    </div>
                                )}
                            </div>

                            <Hr />

                            <PrimaryButton disabled={processing}>
                                Submit
                            </PrimaryButton>
                        </form>
                    </div>
                </Modal>
            </Container>
        </UserDash>
    );
}
