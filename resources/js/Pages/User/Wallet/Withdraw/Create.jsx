import { useForm, usePage } from "@inertiajs/react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import UserDash from "../../../../components/user/dash/UserDash";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import Hr from "../../../../components/Hr";

export default function WithdrawCreate() {
    const {
        available_balance,
        phone,
        errors: pageErrors = {},
    } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        pay_by: "",
        amount: "",
        pay_to: "",
        phone: phone || "",
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("user.wallet.withdraw.store"), {
            preserveScroll: true,
        });
    };

    const payByError = errors.pay_by || pageErrors.pay_by;
    const amountError = errors.amount || pageErrors.amount;
    const payToError = errors.pay_to || pageErrors.pay_to;
    const phoneError = errors.phone || pageErrors.phone;

    return (
        <UserDash>
            <div>
                <div className="mb-2 text-xl font-semibold">
                    Request For A Withdraw
                </div>

                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Withdraw Request"
                            content={
                                available_balance > 1 ? (
                                    <div>
                                        Able to Withdraw : {available_balance}
                                    </div>
                                ) : (
                                    <span>
                                        You need to meet minimum balance to make
                                        a successful withdraw. Withdrawable
                                        balance :{" "}
                                        <strong className="text-red-900">
                                            {available_balance}
                                        </strong>{" "}
                                        TK
                                    </span>
                                )
                            }
                        />

                        <SectionInner>
                            <form onSubmit={submit}>
                                <div className="mb-3 grid gap-4 md:grid-cols-2">
                                    <div className="md:col-span-2">
                                        <label className="block mb-1 text-sm font-medium text-gray-700">
                                            Payment Method
                                        </label>
                                        <select
                                            name="pay_by"
                                            id="bank_name"
                                            value={data.pay_by}
                                            onChange={(e) =>
                                                setData(
                                                    "pay_by",
                                                    e.target.value,
                                                )
                                            }
                                            className="border-0 rounded ring-1 shadow-0 form-control form-select"
                                        >
                                            <option value="">
                                                Payment Method
                                            </option>
                                            <option value="bkash">Bkash</option>
                                            <option value="nogod">Nogod</option>
                                            <option value="roket">Roket</option>
                                        </select>
                                        {payByError && (
                                            <div className="block text-xs text-red-900">
                                                {payByError}
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block mb-1 text-sm font-medium text-gray-700">
                                            Amount
                                        </label>
                                        <input
                                            type="number"
                                            name="amount"
                                            value={data.amount}
                                            onChange={(e) =>
                                                setData(
                                                    "amount",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Amount"
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        {amountError && (
                                            <div className="block text-xs text-red-900">
                                                {amountError}
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block mb-1 text-sm font-medium text-gray-700">
                                            Payment Number
                                        </label>
                                        <input
                                            type="number"
                                            name="pay_to"
                                            id="account"
                                            value={data.pay_to}
                                            onChange={(e) =>
                                                setData(
                                                    "pay_to",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Enter Payment Number"
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        {payToError && (
                                            <div className="block text-xs text-red-900">
                                                {payToError}
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block mb-1 text-sm font-medium text-gray-700">
                                            Contact Number
                                        </label>
                                        <input
                                            type="number"
                                            name="phone"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            placeholder="Your Contact Number"
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        {phoneError && (
                                            <div className="block text-xs text-red-900">
                                                {phoneError}
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <Hr />
                                <div className="flex items-center justify-end space-x-2 text-end">
                                    <NavLink
                                        href={route("user.wallet.withdraw")}
                                    >
                                        <i className="mr-2 fas fa-arrow-left"></i>{" "}
                                        Back
                                    </NavLink>
                                    <PrimaryButton disabled={processing}>
                                        Submit
                                    </PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>
        </UserDash>
    );
}
