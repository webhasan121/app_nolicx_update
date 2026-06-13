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
import useTranslation from "../../../../hooks/useTranslation";
import { formatAmount } from "../../../../utils/formatAmount";

export default function DepositHistory() {
    const { t } = useTranslation();
    const { coin = 0, history = [], payNumbers = {} } = usePage().props;
    const [showModal, setShowModal] = useState(false);
    const [copiedType, setCopiedType] = useState("");
    const depositPayNumbers = Array.isArray(payNumbers)
        ? payNumbers
        : Object.entries(payNumbers).map(([name, value]) => ({ name, value }));

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

    const copyNumber = async (type, number) => {
        if (!number) return;

        if (window.navigator?.clipboard?.writeText) {
            await window.navigator.clipboard.writeText(number);
        } else {
            const input = document.createElement("input");
            input.value = number;
            document.body.appendChild(input);
            input.select();
            document.execCommand("copy");
            document.body.removeChild(input);
        }

        setCopiedType(type);
        window.setTimeout(() => setCopiedType(""), 1200);
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("Deposit To Wallet")}
                        content={
                            <div className="items-center justify-between md:flex">
                                <div className="text-2xl font-bold text-indigo-900">
                                    {formatAmount(coin)} TK
                                </div>
                                <div className="flex">
                                    <NavLinkBtn
                                        className="px-2 border-0 rounded-lg ring-1 uppercase font-bold"
                                        href={route("user.wallet.withdraw")}
                                    >
                                        {t("Withdraw")}
                                    </NavLinkBtn>
                                </div>
                            </div>
                        }
                    />
                </SectionSection>

                <SectionSection>
                    <div className="text-sm">
                        {t("Deposit amount to your wallet. To make confirm your deposit, you are requested to send your expected amout to our configured payment account.")}
                        <div className="flex flex-wrap gap-2 mt-2">
                            {depositPayNumbers.length ? (
                                depositPayNumbers.map((item, index) => (
                                    <div
                                        key={`${item.name}-${item.value}-${index}`}
                                        className="inline-flex items-center p-2 mb-1 border rounded"
                                    >
                                        <span className="pr-2 font-bold">{item.name}:</span>{" "}
                                        <span>{item.value}</span>
                                        <button
                                            type="button"
                                            className="inline-flex items-center justify-center w-7 h-7 ml-2 text-gray-600 border rounded hover:bg-gray-100"
                                            title={t("Copy number")}
                                            onClick={() => copyNumber(item.name, item.value)}
                                        >
                                            <i className="fas fa-copy"></i>
                                        </button>
                                        {copiedType === item.name ? (
                                            <span className="ml-1 text-xs text-green-600">
                                                {t("Copied")}
                                            </span>
                                        ) : null}
                                    </div>
                                ))
                            ) : (
                                <div className="p-2 text-sm text-red-700 border border-red-200 rounded bg-red-50">
                                    {t("No deposit payment number configured.")}
                                </div>
                            )}
                        </div>
                    </div>
                    <Hr />
                    <PrimaryButton onClick={() => setShowModal(true)}>
                        <i className="px-2 fas fa-plus"></i> {t("Deposit")}
                    </PrimaryButton>
                </SectionSection>

                <SectionSection>
                    <div>{t("History")}</div>

                    <SectionInner>
                        <Table data={history} emptyMessage={t("Data Not Found")}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("Amount")}</th>
                                    <th>{t("Payment")}</th>
                                    <th>{t("Trx ID")}</th>
                                    <th>{t("Status")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {history.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{formatAmount(item.amount)}</td>
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
                                                ? t("Confirmed")
                                                : t("Pending")}
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
                        <div className="text-lg">{t("Deposit")}</div>

                        <Hr />

                        <form onSubmit={submit}>
                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-amount">{t("Amount")}</InputLabel>
                                <TextInput
                                    id="deposit-amount"
                                    type="number"
                                    value={data.amount}
                                    onChange={(e) =>
                                        setData("amount", e.target.value)
                                    }
                                    className="w-full"
                                    placeholder={t("Enter amount")}
                                />
                                {errors.amount && (
                                    <div className="text-red-500">
                                        {errors.amount}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-paymentMethod">{t("Payment Method")}</InputLabel>
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
                                        {t("Select Payment Method")}
                                    </option>
                                    {depositPayNumbers.map((item, index) => (
                                        <option
                                            key={`${item.name}-${index}`}
                                            value={item.name}
                                        >
                                            {item.name}
                                        </option>
                                    ))}
                                    <option value="Bank">{t("Bank")}</option>
                                </select>
                                {errors.paymentMethod && (
                                    <div className="text-red-500">
                                        {errors.paymentMethod}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-receiverAccountNumber">{t("Receiver Account Number")}</InputLabel>
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
                                    placeholder={t("Enter account number")}
                                />
                                {errors.receiverAccountNumber && (
                                    <div className="text-red-500">
                                        {errors.receiverAccountNumber}
                                    </div>
                                )}
                                <div className="text-xs">
                                    {t("If you send throught the bank, your are requested to write Bank Name first. Then Back Account Number.")}
                                </div>
                            </div>

                            <Hr />
                            <div className="text-xs">{t("Sender Info")}</div>
                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-senderAccountNumber">{t("Sender Account Number")}</InputLabel>
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
                                    placeholder={t("Enter account number")}
                                />
                                {errors.senderAccountNumber && (
                                    <div className="text-red-500">
                                        {errors.senderAccountNumber}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-senderName">{t("Sender Name")}</InputLabel>
                                <TextInput
                                    id="deposit-senderName"
                                    type="text"
                                    value={data.senderName}
                                    onChange={(e) =>
                                        setData("senderName", e.target.value)
                                    }
                                    className="w-full"
                                    placeholder={t("Enter sender name")}
                                />
                                {errors.senderName && (
                                    <div className="text-red-500">
                                        {errors.senderName}
                                    </div>
                                )}
                            </div>

                            <div className="mb-4">
                                <InputLabel htmlFor="deposit-transactionId">{t("Transaction ID")}</InputLabel>
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
                                    placeholder={t("Enter transaction ID")}
                                />
                                {errors.transactionId && (
                                    <div className="text-red-500">
                                        {errors.transactionId}
                                    </div>
                                )}
                            </div>

                            <Hr />

                            <PrimaryButton disabled={processing}>
                                {t("Submit")}
                            </PrimaryButton>
                        </form>
                    </div>
                </Modal>
            </Container>
        </UserDash>
    );
}
