import { useForm } from "@inertiajs/react";
import { useState } from "react";
import Modal from "../../../components/Modal";
import InputLabel from "../../../components/InputLabel";
import NavLink from "../../../components/NavLink";
import TextInput from "../../../components/TextInput";
import PrimaryButton from "../../../components/PrimaryButton";

export default function CoinStore({ store = 0, take = 0, give = 0 }) {
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
        form.post(route("system.store.withdraw.coin"), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                form.reset();
            },
        });
    };

    return (
        <div>
            <div className="w-full text-center bg-white rounded ">
                <div className="items-start justify-between p-2 border border-green-900 rounded md:flex">
                    <div className="flex items-center justify-between px-3 py-1 text-center lg:p-3 bold md:block">
                        <div className="text-sm fs-5 fw-bold text-start">
                            <NavLink
                                href="#"
                                className="flex items-center p-0 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                                onClick={(e) => e.preventDefault()}
                            >
                                <i className="p-2 fas fa-store fs-6"></i>
                                Comission Store
                            </NavLink>
                        </div>
                        <div className="relative mt-2">
                            {/* <button
                                type="button"
                                onClick={() => setOpen(true)}
                                className="inline-block px-3 py-1 bg-blue-500 rounded-md hover:bg-blue-600"
                            >
                                <span className="text-sm font-bold text-white">
                                    Withdraw
                                </span>
                            </button> */}
                        </div>
                    </div>
                    <div className="px-3 py-1 text-lg text-center text-green-900 lg:p-3 fw-bold">
                        <div className="px-2 font-bold border rounded">
                            {store}
                        </div>

                        <div className="py-2 ">
                            <div className="flex items-center justify-center text-xs">
                                <div className="text-red-900 text-start" style={{ color: "red" }}>
                                    {give}
                                    <i className="fas fa-long-arrow-alt-up"></i>
                                </div>
                                <div className="px-3">|</div>
                                <div className="text-green-900" style={{ color: "green" }}>
                                    <i className="fas fa-long-arrow-alt-down"></i>
                                    {take}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="px-3 py-1 lg:p-3 text-end">
                        <div className="flex items-center text-xs">
                            <div className="text-red-900 text-start" style={{ color: "red" }}>
                                <i className="fas fa-long-arrow-alt-up"></i>
                            </div>
                            <div className="px-3">|</div>
                            <div className="text-green-900" style={{ color: "green" }}>
                                <i className="fas fa-long-arrow-alt-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Modal show={open} onClose={() => setOpen(false)} maxWidth="md">
                <div className="p-3">Withdraw</div>
                <hr className="my-2" />
                <div className="p-4">
                    <form onSubmit={submit}>
                        <div className="grid grid-cols-2 gap-6">
                            <div className="relative w-full">
                                <InputLabel htmlFor="coin-method">Payment Method</InputLabel>
                                <select
                                    id="coin-method"
                                    value={form.data.method}
                                    onChange={(e) => form.setData("method", e.target.value)}
                                    className="w-full py-2 rounded-md"
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
                                <InputLabel htmlFor="coin-amount">Withdraw Amount</InputLabel>
                                <TextInput
                                    id="coin-amount"
                                    type="number"
                                    value={form.data.amount}
                                    onChange={(e) => form.setData("amount", e.target.value)}
                                    className="w-full"
                                    placeholder="Enter withdraw amount"
                                />
                                {form.errors.amount ? (
                                    <span className="text-sm text-red-500">{form.errors.amount}</span>
                                ) : null}
                            </div>
                        </div>

                        {form.data.method === "Bank" ? (
                            <>
                                <div className="relative my-4">
                                    <InputLabel htmlFor="coin-bankAccount">Bank Account</InputLabel>
                                    <TextInput
                                        id="coin-bankAccount"
                                        type="text"
                                        value={form.data.bankAccount}
                                        onChange={(e) => form.setData("bankAccount", e.target.value)}
                                        className="w-full"
                                        placeholder="Enter bank account"
                                    />
                                </div>

                                <div className="relative my-4">
                                    <InputLabel htmlFor="coin-accountholder">Account Holder Name</InputLabel>
                                    <TextInput
                                        id="coin-accountholder"
                                        type="text"
                                        value={form.data.accountholder}
                                        onChange={(e) => form.setData("accountholder", e.target.value)}
                                        className="w-full"
                                        placeholder="Account holder name"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-6 my-4">
                                    <div className="relative">
                                        <InputLabel htmlFor="coin-bankBranch">Bank Branch</InputLabel>
                                        <TextInput
                                            id="coin-bankBranch"
                                            type="text"
                                            value={form.data.bankBranch}
                                            onChange={(e) => form.setData("bankBranch", e.target.value)}
                                            className="w-full"
                                            placeholder="Enter bank branch"
                                        />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="coin-swiftCode">Swift Code</InputLabel>
                                        <TextInput
                                            id="coin-swiftCode"
                                            type="text"
                                            value={form.data.swiftCode}
                                            onChange={(e) => form.setData("swiftCode", e.target.value)}
                                            className="w-full"
                                            placeholder="Enter swift code"
                                        />
                                    </div>
                                </div>

                                <div className="relative my-4">
                                    <InputLabel htmlFor="coin-accountNumber">Account Number</InputLabel>
                                    <TextInput
                                        id="coin-accountNumber"
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
                                <InputLabel htmlFor="coin-phone">Phone Number</InputLabel>
                                <TextInput
                                    id="coin-phone"
                                    type="number"
                                    value={form.data.phone}
                                    onChange={(e) => form.setData("phone", e.target.value)}
                                    className="w-full"
                                    placeholder="Enter phone number"
                                />
                            </div>
                        )}

                        <div className="relative my-4">
                            <InputLabel htmlFor="coin-remarks">Remarks</InputLabel>
                            <textarea
                                id="coin-remarks"
                                rows="3"
                                value={form.data.remarks}
                                onChange={(e) => form.setData("remarks", e.target.value)}
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
