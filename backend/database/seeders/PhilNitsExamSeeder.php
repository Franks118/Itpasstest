<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilNitsExamSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = $this->ensureCreator();
        
        $exam = Exam::query()->updateOrCreate(
            ['title' => 'PhilNITS FE High-Probability'],
            [
                'user_id' => $creatorId,
                'description' => 'Targeted practice for topics most likely to appear in the PhilNITS Fundamental IT Engineer (FE) exam.',
                'duration_minutes' => 90,
                'status' => 'published',
                'total_questions' => 0,
                'shuffle_questions' => true,
            ]
        );

        $exam->questions()->delete();

        $questions = [
            // Original 10 questions
            [
                'topic' => 'Algorithms and programming',
                'text' => 'In a stack data structure, which principle is used for inserting and removing elements?',
                'explanation' => 'Stacks use LIFO (Last-In-First-Out), while Queues use FIFO (First-In-First-Out).',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'LIFO (Last-In-First-Out)', 'correct' => true],
                    ['text' => 'FIFO (First-In-First-Out)', 'correct' => false],
                    ['text' => 'LRU (Least Recently Used)', 'correct' => false],
                    ['text' => 'Random Access', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which memory management technique allows a computer to compensate for physical RAM shortages by temporarily transferring data from RAM to disk storage?',
                'explanation' => 'Virtual memory uses hardware and software to allow a computer to compensate for physical memory shortages.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Virtual Memory', 'correct' => true],
                    ['text' => 'Cache Memory', 'correct' => false],
                    ['text' => 'Read-Only Memory (ROM)', 'correct' => false],
                    ['text' => 'EEPROM', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'Which of the following is a private IP address range as defined by RFC 1918?',
                'explanation' => '192.168.0.0 – 192.168.255.255 is one of the three reserved private IP ranges.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '192.168.0.0/16', 'correct' => true],
                    ['text' => '8.8.8.8', 'correct' => false],
                    ['text' => '172.32.0.0/12', 'correct' => false],
                    ['text' => '10.256.0.0/8', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'Which normalization form focuses on removing transitive dependencies?',
                'explanation' => '3NF (Third Normal Form) requires that all non-key attributes are dependent only on the primary key (no transitive dependencies).',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '3NF', 'correct' => true],
                    ['text' => '1NF', 'correct' => false],
                    ['text' => '2NF', 'correct' => false],
                    ['text' => 'BCNF', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'In asymmetric encryption, which key is used to create a digital signature that the sender can use to prove their identity?',
                'explanation' => 'The sender uses their PRIVATE key to sign; anyone with the sender\'s PUBLIC key can verify it.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Sender\'s Private Key', 'correct' => true],
                    ['text' => 'Sender\'s Public Key', 'correct' => false],
                    ['text' => 'Receiver\'s Public Key', 'correct' => false],
                    ['text' => 'Common Session Key', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Project management',
                'text' => 'What is the "Critical Path" in a project network diagram?',
                'explanation' => 'The critical path is the longest path of planned activities to the end of the project, and the earliest date that the project can settle.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'The longest path representing the minimum project duration', 'correct' => true],
                    ['text' => 'The path with the most tasks', 'correct' => false],
                    ['text' => 'The path with the least cost', 'correct' => false],
                    ['text' => 'The shortest path to completion', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Which logic gate produces an output of 1 (True) ONLY if both inputs are 1?',
                'explanation' => 'The AND gate requires all inputs to be true to output true.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'AND', 'correct' => true],
                    ['text' => 'OR', 'correct' => false],
                    ['text' => 'XOR', 'correct' => false],
                    ['text' => 'NAND', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Service management',
                'text' => 'What is an SLA (Service Level Agreement)?',
                'explanation' => 'An SLA is a formal contract between a service provider and a customer defining the level of service expected.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'A contract defining expected service levels', 'correct' => true],
                    ['text' => 'A type of software license', 'correct' => false],
                    ['text' => 'A network security protocol', 'correct' => false],
                    ['text' => 'A database backup strategy', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'Which testing phase focuses on verifying that different modules or sub-systems work together correctly?',
                'explanation' => 'Integration testing focuses on the interfaces and interactions between modules.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Integration Testing', 'correct' => true],
                    ['text' => 'Unit Testing', 'correct' => false],
                    ['text' => 'System Testing', 'correct' => false],
                    ['text' => 'Regression Testing', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'What is the "hit rate" in the context of CPU cache?',
                'explanation' => 'Hit rate is the fraction of memory accesses that are found in the cache.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'The percentage of accesses found in the cache', 'correct' => true],
                    ['text' => 'The speed of the CPU clock', 'correct' => false],
                    ['text' => 'The number of instructions per second', 'correct' => false],
                    ['text' => 'The disk transfer speed', 'correct' => false],
                ]
            ],
            // New 20 questions
            [
                'topic' => 'Basic theory',
                'text' => 'What is the decimal representation of the binary number 101101?',
                'explanation' => '101101 in binary is (1 * 2^5) + (0 * 2^4) + (1 * 2^3) + (1 * 2^2) + (0 * 2^1) + (1 * 2^0) = 32 + 8 + 4 + 1 = 45.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '45', 'correct' => true],
                    ['text' => '37', 'correct' => false],
                    ['text' => '53', 'correct' => false],
                    ['text' => '41', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Using 2\'s complement representation for 8-bit integers, what is the result of -5?',
                'explanation' => '5 is 00000101. 1\'s complement is 11111010. Adding 1 gives 11111011, which is FB in hexadecimal.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '11111011', 'correct' => true],
                    ['text' => '11111010', 'correct' => false],
                    ['text' => '10000101', 'correct' => false],
                    ['text' => '01111011', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'In CPU scheduling, which algorithm gives each process a fixed time slot (quantum) in a cyclic manner?',
                'explanation' => 'Round Robin (RR) scheduling uses time slices to ensure fair CPU sharing among processes.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Round Robin', 'correct' => true],
                    ['text' => 'First-Come, First-Served', 'correct' => false],
                    ['text' => 'Shortest Job First', 'correct' => false],
                    ['text' => 'Priority Scheduling', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which of the following is a characteristic of RISC (Reduced Instruction Set Computer) architecture?',
                'explanation' => 'RISC focuses on simple instructions that can be executed in a single clock cycle, often using a large number of registers.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Uniform instruction length and single-cycle execution', 'correct' => true],
                    ['text' => 'Complex instructions that take multiple cycles', 'correct' => false],
                    ['text' => 'Variable instruction formats', 'correct' => false],
                    ['text' => 'Minimized use of general-purpose registers', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'According to De Morgan\'s Laws, NOT (A AND B) is equivalent to which of the following?',
                'explanation' => 'De Morgan\'s Law states that the negation of a conjunction is the disjunction of the negations: !(A && B) = !A || !B.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '(NOT A) OR (NOT B)', 'correct' => true],
                    ['text' => '(NOT A) AND (NOT B)', 'correct' => false],
                    ['text' => 'NOT (A OR B)', 'correct' => false],
                    ['text' => 'A OR B', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'In the waterfall model of software development, which phase typically follows "Requirements Analysis"?',
                'explanation' => 'In a standard waterfall model, the System/Architectural Design phase follows Requirements Analysis.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'System Design', 'correct' => true],
                    ['text' => 'Coding', 'correct' => false],
                    ['text' => 'Testing', 'correct' => false],
                    ['text' => 'Maintenance', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'Which SQL clause is used to filter records AFTER an aggregation has been performed using GROUP BY?',
                'explanation' => 'WHERE filters rows before grouping; HAVING filters groups after aggregation.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'HAVING', 'correct' => true],
                    ['text' => 'WHERE', 'correct' => false],
                    ['text' => 'ORDER BY', 'correct' => false],
                    ['text' => 'SELECT', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'In a relational database, what property ensures that a transaction is all-or-nothing (either fully completed or fully reverted)?',
                'explanation' => 'Atomicity is the "A" in ACID, ensuring that all parts of a transaction succeed or none do.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Atomicity', 'correct' => true],
                    ['text' => 'Consistency', 'correct' => false],
                    ['text' => 'Isolation', 'correct' => false],
                    ['text' => 'Durability', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'At which layer of the OSI model does a standard "Switch" (not a multilayer switch) primarily operate?',
                'explanation' => 'Standard network switches operate at Layer 2, the Data Link layer, using MAC addresses.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Data Link Layer (Layer 2)', 'correct' => true],
                    ['text' => 'Network Layer (Layer 3)', 'correct' => false],
                    ['text' => 'Physical Layer (Layer 1)', 'correct' => false],
                    ['text' => 'Transport Layer (Layer 4)', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'Which protocol is responsible for resolving a known IP address into a physical MAC address?',
                'explanation' => 'ARP (Address Resolution Protocol) maps Layer 3 (IP) addresses to Layer 2 (MAC) addresses.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'ARP', 'correct' => true],
                    ['text' => 'DNS', 'correct' => false],
                    ['text' => 'DHCP', 'correct' => false],
                    ['text' => 'HTTP', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'What is the primary purpose of a "Honey Pot" in network security?',
                'explanation' => 'A honeypot is a decoy system designed to lure, detect, and study the actions of attackers.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'To lure and study potential attackers', 'correct' => true],
                    ['text' => 'To speed up network traffic', 'correct' => false],
                    ['text' => 'To back up sensitive data', 'correct' => false],
                    ['text' => 'To provide free Wi-Fi to guests', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'Which type of malware replicates itself automatically across a network without needing to attach to an existing file or program?',
                'explanation' => 'Worms are self-replicating and spread over networks; viruses need a host file to attach to.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Worm', 'correct' => true],
                    ['text' => 'Virus', 'correct' => false],
                    ['text' => 'Trojan Horse', 'correct' => false],
                    ['text' => 'Spyware', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which RAID level uses disk mirroring to provide high data redundancy without using parity?',
                'explanation' => 'RAID 1 uses mirroring, copying all data from one disk to another for redundancy.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'RAID 1', 'correct' => true],
                    ['text' => 'RAID 0', 'correct' => false],
                    ['text' => 'RAID 5', 'correct' => false],
                    ['text' => 'RAID 10', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Algorithms and programming',
                'text' => 'Which sorting algorithm has a worst-case time complexity of O(n^2) but is often faster in practice than Merge Sort for small datasets?',
                'explanation' => 'Quick Sort has O(n^2) worst case but O(n log n) average, and often outperforms others due to low overhead.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Quick Sort', 'correct' => true],
                    ['text' => 'Merge Sort', 'correct' => false],
                    ['text' => 'Heap Sort', 'correct' => false],
                    ['text' => 'Bubble Sort', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Project management',
                'text' => 'In PERT (Program Evaluation and Review Technique), which formula is used to calculate the Expected Time (Te)?',
                'explanation' => 'Expected Time (Te) = (Optimistic + 4*Most Likely + Pessimistic) / 6.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '(O + 4M + P) / 6', 'correct' => true],
                    ['text' => '(O + M + P) / 3', 'correct' => false],
                    ['text' => '(O + 2M + P) / 4', 'correct' => false],
                    ['text' => '4M + (O+P)/2', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Service management',
                'text' => 'Which phase of the PDCA (Plan-Do-Check-Act) cycle involves evaluating the results of a pilot implementation?',
                'explanation' => 'The "Check" phase is where results are evaluated against the original plan and objectives.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Check', 'correct' => true],
                    ['text' => 'Plan', 'correct' => false],
                    ['text' => 'Do', 'correct' => false],
                    ['text' => 'Act', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Corporate and legal affairs',
                'text' => 'Which legal protection is most appropriate for a unique invention like a new type of semiconductor manufacturing process?',
                'explanation' => 'Patents protect inventions and technical processes for a limited period.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Patent', 'correct' => true],
                    ['text' => 'Copyright', 'correct' => false],
                    ['text' => 'Trademark', 'correct' => false],
                    ['text' => 'Trade Secret', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Business strategy',
                'text' => 'Which technique is used to analyze the competitive environment by looking at: Threat of New Entrants, Bargaining Power of Buyers/Suppliers, Threat of Substitutes, and Rivalry?',
                'explanation' => 'Porter\'s Five Forces framework is used for analyzing industry structure and competitive strategy.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Porter\'s Five Forces', 'correct' => true],
                    ['text' => 'SWOT Analysis', 'correct' => false],
                    ['text' => 'PPM (Product Portfolio Management)', 'correct' => false],
                    ['text' => 'Balance Scorecard', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Convert the hexadecimal number 2F to decimal.',
                'explanation' => '2F = (2 * 16^1) + (15 * 16^0) = 32 + 15 = 47.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '47', 'correct' => true],
                    ['text' => '31', 'correct' => false],
                    ['text' => '42', 'correct' => false],
                    ['text' => '51', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'Which software testing technique focuses on testing the internal structure and logic of the code (seeing the "inside")?',
                'explanation' => 'White-box testing (or structural testing) evaluates internal paths and code logic.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'White-box Testing', 'correct' => true],
                    ['text' => 'Black-box Testing', 'correct' => false],
                    ['text' => 'Acceptance Testing', 'correct' => false],
                    ['text' => 'Usability Testing', 'correct' => false],
                ]
            ]
        ];

        $topicMap = Topic::query()->pluck('id', 'name');
        $count = 0;

        foreach ($questions as $qData) {
            $topicId = $topicMap[$qData['topic']] ?? null;
            if (!$topicId) {
                // Fallback to major category search if exact name mismatch
                $topicId = Topic::query()->where('major_category', 'like', "%{$qData['topic']}%")->value('id') ?? 1;
            }

            $q = $exam->questions()->create([
                'topic_id' => $topicId,
                'question_text' => $qData['text'],
                'explanation' => $qData['explanation'],
                'difficulty' => $qData['difficulty'],
                'points' => 1,
                'order_index' => ++$count,
            ]);

            foreach ($qData['options'] as $idx => $o) {
                $q->options()->create([
                    'option_text' => $o['text'],
                    'is_correct' => $o['correct'],
                    'order_index' => $idx + 1,
                ]);
            }
        }

        $exam->update(['total_questions' => $count]);
    }

    private function ensureCreator(): int
    {
        return DB::table('learners')->orderBy('id')->value('id') ?? 
               DB::table('learners')->insertGetId([
                   'name' => 'PhilNITS Official',
                   'session_number' => 1,
                   'score' => 0,
                   'timestamp' => now(),
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
    }
}
