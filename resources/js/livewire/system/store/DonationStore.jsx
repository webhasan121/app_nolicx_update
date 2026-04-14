import { useForm } from "@inertiajs/react";
import { useState } from "react";
import Modal from "../../../components/Modal";
import InputLabel from "../../../components/InputLabel";
import NavLink from "../../../components/NavLink";
import TextInput from "../../../components/TextInput";
import PrimaryButton from "../../../components/PrimaryButton";

export default function DonationStore({ store = 0 }) {
    const [open, setOpen] = useState(false);
    const form = useForm({
        method: "",
        amount: "",
        phone: "",
        bankAccount: "",
        accountholder: "",
        bankBranch: "",
        swiftCode: "",
        accountNumber: "",
        remarks: "",
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route("system.store.withdraw.donation"), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                form.reset();
            },
        });
    };

    return (
        <div className="">
            <div className="rounded bg-white text-center">
                <div className="border border-green-900 rounded md:flex justify-between items-center p-2">
                    <div className="px-3 lg:p-3 bold text-start flex justify-between items-center md:block">
                        <div className="fs-5 fw-bold text-sm text-start">
                            <NavLink
                                href="#"
                                className="flex items-center"
                                unstyled
                                onClick={(e) => e.preventDefault()}
                            >
                                <i className="fas fa-store fs-6 pe-2"></i>
                                Donation
                            </NavLink>
                        </div>
                        <div className="hidden flex items-center text-xs">
                            <div className="text-start text-red-900">
                                <i className="fas fa-long-arrow-alt-up"></i>
                            </div>
                            <div className="px-3">|</div>
                            <div className="text-green-900">
                                <i className="fas fa-long-arrow-alt-down"></i>
                            </div>
                        </div>
                    </div>
                    <div className="px-3 py-1 lg:p-3 text-lg fw-bold text-green-900">
                        {store}
                    </div>
                </div>
            </div>
            <div className="relative mt-2">
                <button
                    type="button"
                    onClick={() => setOpen(true)}
                    className="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-3 py-1"
                >
                    <span className="text-sm text-white font-bold">Withdraw</span>
                </button>
            </div>

            <Modal show={open} onClose={() => setOpen(false)} maxWidth="md">
                <div className="p-3">Withdraw</div>
                <hr className="my-2" />
                <div className="p-4">
                    <form onSubmit={submit}>
                        <div className="grid grid-cols-2 gap-6">
                            <div className="relative w-full">
                                <InputLabel htmlFor="donation-method">Payment Method</InputLabel>
                                <select
                                    id="donation-method"
                                    value={form.data.method}
                                    onChange={(e) => form.setData("method", e.target.value)}
                                    className="py-2 rounded-md w-full"
                                >
                                    <option value=""> -- Choose -- </option>
                                    {["Bkash", "Nogod", "Rocket", "Bank"].map((item) => (
                                        <option key={item} value={item}>
                                            {item}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="relative">
                                <InputLabel htmlFor="donation-amount">Withdraw Amount</InputLabel>
                                <TextInput
                                    id="donation-amount"
                                    type="number"
                                    value={form.data.amount}
                                    onChange={(e) => form.setData("amount", e.target.value)}
                                    className="w-full"
                                    placeholder="Enter withdraw amount"
                                />
                                {form.errors.amount ? (
                                    <span className="text-red-500 text-sm">{form.errors.amount}</span>
                                ) : null}
                            </div>
                        </div>

                        {form.data.method === "Bank" ? (
                            <>
                                <div className="relative my-4">
                                    <InputLabel htmlFor="donation-bankAccount">Bank Account</InputLabel>
                                    <TextInput
                                        id="donation-bankAccount"
                                        type="text"
                                        value={form.data.bankAccount}
                                        onChange={(e) => form.setData("bankAccount", e.target.value)}
                                        className="w-full"
                                        placeholder="Enter bank account"
                                    />
                                </div>

                                <div className="relative my-4">
                                    <InputLabel htmlFor="donation-accountholder">Account Holder Name</InputLabel>
                                    <TextInput
                                        id="donation-accountholder"
                                        type="text"
                                        value={form.data.accountholder}
                                        onChange={(e) => form.setData("accountholder", e.target.value)}
                                        className="w-full"
                                        placeholder="Account holder name"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-6 my-4">
                                    <div className="relative">
                                        <InputLabel htmlFor="donation-bankBranch">Bank Branch</InputLabel>
                                        <TextInput
                                            id="donation-bankBranch"
                                            type="text"
                                            value={form.data.bankBranch}
                                            onChange={(e) => form.setData("bankBranch", e.target.value)}
                                            className="w-full"
                                            placeholder="Enter bank branch"
                                        />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="donation-swiftCode">Swift Code</InputLabel>
                                        <TextInput
                                            id="donation-swiftCode"
                                            type="text"
                                            value={form.data.swiftCode}
                                            onChange={(e) => form.setData("swiftCode", e.target.value)}
                                            className="w-full"
                                            placeholder="Enter swift code"
                                        />
                                    </div>
                                </div>

                                <div className="relative my-4">
                                    <InputLabel htmlFor="donation-accountNumber">Account Number</InputLabel>
                                    <TextInput
                                        id="donation-accountNumber"
                                        type="text"
                                        value={form.data.accountNumber}
                                        onChange={(e) => form.setData("accountNumber", e.target.value)}
                                        className="w-full"
                                        placeholder="Enter account number"
                                    />
                                </div>
                            </>
                        ) : (
                            <div className="relative my-4">
                                <InputLabel htmlFor="donation-phone">Phone Number</InputLabel>
                                <TextInput
                                    id="donation-phone"
                                    type="number"
                                    value={form.data.phone}
                                    onChange={(e) => form.setData("phone", e.target.value)}
                                    className="w-full"
                                    placeholder="Enter phone number"
                                />
                            </div>
                        )}

                        <div className="relative my-4">
                            <InputLabel htmlFor="donation-remarks">Remarks</InputLabel>
                            <textarea
                                id="donation-remarks"
                                rows="3"
                                value={form.data.remarks}
                                onChange={(e) => form.setData("remarks", e.target.value)}
                                className="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            ></textarea>
                        </div>

                        <div className="flex justify-end">
                            <PrimaryButton type="submit" disabled={form.processing}>
                                Submit
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    );
}
