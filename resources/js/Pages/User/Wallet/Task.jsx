import { usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";

export default function Task() {
    const { tasks = [] } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Your Tasks"
                        content="Your task and earnings"
                    />

                    <SectionInner>
                        <Table data={tasks}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Earning</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                {tasks.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.date}</td>
                                        <td>{item.earning}</td>
                                        <td>{item.time}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}

