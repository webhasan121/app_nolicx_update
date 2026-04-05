import { usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import SecondaryButton from "../../../../components/SecondaryButton";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Foreach from "../../../../components/dashboard/Foreach";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";

export default function Index() {
    const { condition = "Active", widgets = [], riders } = usePage().props;

    return (
        <AppLayout
            title="Rider - Delevary Man"
            header={<PageHeader>Rider - Delevary Man</PageHeader>}
        >
            <div>
                <Container>
                    <OverviewSection>
                        {widgets.map((item) => (
                            <OverviewDiv
                                key={item.title}
                                title={item.title}
                                content={item.content}
                            />
                        ))}
                    </OverviewSection>
                </Container>

                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Riders"
                            content={
                                <>
                                    <div className="flex justify-between items-center">
                                        <div>
                                            <NavLink active={condition === "all"} href={route("system.rider.index", { condition: "all" })}> All </NavLink>
                                            <NavLink active={condition === "Active"} href={route("system.rider.index", { condition: "Active" })}> Active </NavLink>
                                            <NavLink active={condition === "Pending"} href={route("system.rider.index", { condition: "Pending" })}> Pending </NavLink>
                                            <NavLink active={condition === "Disabled"} href={route("system.rider.index", { condition: "Disabled" })}> Disabled </NavLink>
                                            <NavLink active={condition === "Suspended"} href={route("system.rider.index", { condition: "Suspended" })}> Suspended </NavLink>
                                        </div>

                                        <div className="flex">
                                            <SecondaryButton>Filter</SecondaryButton>
                                        </div>
                                    </div>
                                    <div className="text-xs">
                                        {riders?.count ?? "0"} {condition} Riders
                                    </div>
                                </>
                            }
                        />

                        <SectionInner>
                            <Foreach data={riders?.data ?? []}>
                                <Table data={riders?.data ?? []}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Join Data</th>
                                            <th>A/C</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {(riders?.data ?? []).map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.sl}</td>
                                                <td>{item.user_name}</td>
                                                <td>{item.status}</td>
                                                <td>
                                                    {item.created_at_formatted}
                                                    <br />
                                                    <span className="text-xs">
                                                        {item.created_at_human}
                                                    </span>
                                                </td>
                                                <td>
                                                    <NavLink href={route("system.rider.edit", { id: item.id })}>
                                                        edit
                                                    </NavLink>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </Table>
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>
        </AppLayout>
    );
}
